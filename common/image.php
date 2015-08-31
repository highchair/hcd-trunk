<?php
	// TODO: full cache framework, including conditional gets, etc
	function set_image_cache_headers($unique_token)
	{
	    $expireTime = gmdate('D, d M Y H:i:s', strtotime("+30 days"));
	    header('Cache-Control: private');
	    header('Pragma: ');
	    header("Expires: {$expireTime} GMT");
	    $eTag = '"'.md5($unique_token).'"';
	    header('Etag: '.$eTag);
	    //header('Last-Modified: '.gmdate('D, d M Y H:i:s', mktime(0, 0, 0, 12, 32, 1997)).' GMT');
	}
	
	function open_image( $file ) {
		
		list( $width, $height, $type, $attr ) = getimagesize( $file );
		
		//echo "Type: ".$type."<br />"; 
		
		// http://www.php.net/manual/en/function.exif-imagetype.php
		/*1	IMAGETYPE_GIF
        2	IMAGETYPE_JPEG
        3	IMAGETYPE_PNG
        4	IMAGETYPE_SWF
        5	IMAGETYPE_PSD
        6	IMAGETYPE_BMP
        7	IMAGETYPE_TIFF_II (intel byte order)
        8	IMAGETYPE_TIFF_MM (motorola byte order)
        9	IMAGETYPE_JPC
        10	IMAGETYPE_JP2
        11	IMAGETYPE_JPX
        12	IMAGETYPE_JB2
        13	IMAGETYPE_SWC
        14	IMAGETYPE_IFF
        15	IMAGETYPE_WBMP
        16	IMAGETYPE_XBM
        17	IMAGETYPE_ICO */
		
		if ( $type == 2 ) {
        	$im = @imagecreatefromjpeg($file); 
    	} elseif ( $type == 1 ) {
        	$im = @imagecreatefromgif($file);
    	} elseif ( $type == 3 ) {
        	$im = @imagecreatefrompng($file);
    	} elseif ( $type == 15 ) {
        	$im = @imagecreatefromwbmp($file);
    	} elseif ( $type == 6 ) {
        	$im = @imagecreatefrombmp($file);
    	} elseif ( $type == 16 ) {
        	$im = @imagecreatefromxbm($file);
    	} else {
        	$im = @imagecreatefromstring( file_get_contents($file) );
    	}
    	
    	/*if ( $type == IMAGETYPE_JPEG ) {
        	$im = @imagecreatefromjpeg($file); 
    	} elseif ( $type == IMAGETYPE_GIF ) {
        	$im = @imagecreatefromgif($file);
    	} elseif ( $type == IMAGETYPE_PNG ) {
        	$im = @imagecreatefrompng($file);
    	} elseif ( $type == IMAGETYPE_WBMP ) {
        	$im = @imagecreatefromwbmp($file);
    	} elseif ( $type == IMAGETYPE_BMP ) {
        	$im = @imagecreatefrombmp($file);
    	} elseif ( $type == IMAGETYPE_XBM ) {
        	$im = @imagecreatefromxbm($file);
    	} else {
        	$im = @imagecreatefromstring(file_get_contents($file));
    	}*/
    	
    	if ( $im !== false ) { 
    	    return $im; 
        } else {
            die ( throwError("Unable to open_image") ); 
        }
    	return false;
	}
	
	function saveImageInFormat($imageResized, $newImageName, $file_format = null)
	{
		// right now this only supports gif/jpg/jpeg 
		if(!$file_format)
		{
			$file_format = getFileExtension($newImageName);
		}
		switch($file_format)
		{
			case "gif":
				header('Content-Type: image/gif');
				ImageGif($imageResized,$newImageName);
				imagedestroy($imageResized); // free up memory
				break;
			case "jpg":
			case "jpeg":
				header('Content-Type: image/jpeg');
				ImageJpeg($imageResized,$newImageName);
				imagedestroy($imageResized);
				break;
		}
	}
	
	// resizes so that no dimension is larger than a max size, and also maintain ratio
	function resizeToMaxDimension($fileAndPath, $maxSize, $format = "jpg")
	{
	    $image = open_image($fileAndPath);
		if ($image === false) { die ( throwError("Unable to open image for resizing") ); }
		$height = imagesy($image);
		$width = imagesx($image);
		
		if($height > $width)
		{
		    // image is tall. constrain the height
		    resizeImageToMax($fileAndPath, 0, $maxSize, $format);
		}
		else
		{
		    resizeImageToMax($fileAndPath, $maxSize, 0, $format);
		}
	}
	
	// resizes so that no dimension is larger than a max size in one direction, and also maintain ratio
	function resizeToMultipleMaxDimensions($fileAndPath, $maxWidth, $maxHeight, $format = "jpg")
	{
	    list( $width, $height, $type, $attr ) = getimagesize( $fileAndPath );
	    
	    //echo "File and Path: ".$fileAndPath."<br />"; 
	    //echo "Width: ".$width."<br />"; 
	    //echo "Height: ".$height."<br />"; 
	    //echo "Type: ".$format."<br />"; 
	    
	    $oversized_x = ( $width > $maxWidth ) ? true : false;
	    $oversized_y = ( $height > $maxHeight ) ? true : false;
	    
	    //echo "Oversized X: ".$oversized_x."<br />";
	    //echo "Oversized Y: ".$oversized_y."<br />";
		
	    if( $oversized_x && $oversized_y ) {
	        
	        // we're oversized in both dimensions, so pick the smaller maximum to use when resizing
		    if($maxHeight < $maxWidth) {
	    	    resizeImageToMax($fileAndPath, $maxWidth, 0, $format);
	    	} else {
	    	    resizeImageToMax($fileAndPath, 0, $maxHeight, $format);
	    	}
	    } else {
	        if($oversized_x) {
	            resizeImageToMax($fileAndPath, $maxWidth, 0, $format);
	        }
	        if($oversized_y) {
	            resizeImageToMax($fileAndPath, 0, $maxHeight, $format);
	        }
	    }
	}
	
	function resizeImageToMax($fileAndPath, $maxWidth = 0, $maxHeight = 0, $format = 'jpg')
	{
	
		$image = open_image($fileAndPath);
		if ($image === false) { die ( throwError("Unable to open image for resizing") ); }
		$height = imagesy($image);
		$width = imagesx($image);
		
		$new_width = $width;
		$new_height = $height;
	
		// we are resizing proportionally to a max width
		if ( $maxWidth > 0 && $maxHeight == 0 && $width > $maxWidth ) { 
			$ratio = $height / $width;
			$new_width = $maxWidth;
			$new_height = $new_width * $ratio;
		}
		
		// we are resizing proportionally to a max height
		if ( $maxWidth == 0 && $maxHeight > 0 && $height > $maxHeight ) {
			$ratio = $width / $height;
			$new_height = $maxHeight;
			$new_width = $new_height * $ratio;
		}
	
		// we are resizing to absolute size in both directions
		if ( $maxWidth > 0 && $maxHeight > 0 ) {
			$new_height = $maxHeight;
			$new_width = $maxWidth;
		}
	    
		if ($height != $new_height || $width != $new_width){
			$image_resized = imagecreatetruecolor($new_width, $new_height)
			    or die( throwError("&ldquo;imagecreatetruecolor&rdquo; has failed. Check that the GD library is active.") );
			if ( $image_resized !== false ) {
    			if ( imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height) ) {
        			saveImageInFormat($image_resized, $fileAndPath, $format);
    			} else {
        			die( throwError("&ldquo;imagecopyresampled&rdquo; has failed.") ); 
    			}
			}
		} 
	}
	
	function resizeImage($fileAndPath, $width = 0, $height = 0)
	{
		$image = open_image($fileAndPath);
		
		if ($image === false) { die ( throwError("Unable to open image for resizing") ); }
		$height = imagesy($image);
		$width = imagesx($image);
	
		// we are resizing proportionally to a max width
		if($width > 0 && $maxHeight == 0)
		{
			$ratio = $height / $width;
			$new_width = $maxWidth;
			$new_height = $new_width * $ratio;
			//die("new height: $new_height");
		}
	
		if($width == 0 && $maxHeight > 0)
		{
			// we are resizing proportionally to a max height
			$ratio = $width / $height;
			$new_height = $maxHeight;
			$new_width = $new_height * $ratio;
		}
	
		if($width > 0 && $maxHeight > 0)
		{
			// we are resizing to absolute proportions in both directions
			$new_height = $maxHeight;
			$new_width = $maxWidth;
		}
	
		$image_resized = imagecreatetruecolor($new_width, $new_height)
		    or die( throwError("&ldquo;imagecreatetruecolor&rdquo; has failed. Check that the GD library is active.") );
		imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		saveImageInFormat($image_resized, $fileAndPath, "jpg");
	}
	
	// Needs to be used more widely when uploading and saving to the server
	function Upload_and_Save_Image( $image, $table_name, $file_field_name, $row_id, $thiswidth=null, $thisheight=null )
	{
		$mimeType = $image["type"];
		
		switch ($mimeType)
		{
			case "image/gif";				
					$mimeName = "GIF Image";
					break;
			case "image/jpeg";					
					$mimeName = "JPEG Image";
					break;
			case "image/png";				
					$mimeName = "PNG Image";
					break;
			case "image/x-MS-bmp";			 
					$mimeName = "Windows Bitmap";
					break;
			default: 
				 $mimeName = "Unknown image type";
		} 
		$filetype = getFileExtension( $image["name"] );
		list($width) = getimagesize( $image["tmp_name"] );
        
        $max_width = ( defined($thiswidth) ) ? $thiswidth : 0; 
        $max_height = ( defined($thisheight) ) ? $thisheight : 0; 
	
		resizeImageToMax( $image["tmp_name"], $max_width, $max_height, $filetype );
	
		// Open the uploaded file
		$file = fopen( $image["tmp_name"], "r" );
		// Read in the uploaded file
		$fileContents = fread( $file, filesize( $image["tmp_name"] ) ); 
		// Escape special characters in the file
		$fileContents = AddSlashes($fileContents);            
		
		$updateQuery = 'UPDATE '.$table_name.' SET '.$file_field_name.' = "'.$fileContents.'", mime_type = "'.$mimeType.'" WHERE id = '.$row_id.';';
		$result = mysql_Query( $updateQuery, MyActiveRecord::Connection() );
		
		if ( ! $result ) {
            echo( 'Invalid query: ' . mysql_error() );
        } 
	}
	
	// from http://php.net/manual/en/features.file-upload.errors.php
	function get_upload_error( $errnum ) 
	{
    	$upload_errors = array( 
            "0. UPLOAD_ERR_OK: No errors.", 
            "1. UPLOAD_ERR_INI_SIZE: Larger than upload_max_filesize.", 
            "2. UPLOAD_ERR_FORM_SIZE: Larger than form MAX_FILE_SIZE.", 
            "3. UPLOAD_ERR_PARTIAL: Partial upload.", 
            "4. UPLOAD_ERR_NO_FILE: No file.", 
            "6. UPLOAD_ERR_NO_TMP_DIR: No temporary directory.", 
            "7. UPLOAD_ERR_CANT_WRITE: Can't write to disk.", 
            "8. UPLOAD_ERR_EXTENSION: File upload stopped by extension.", 
            "UPLOAD_ERR_EMPTY: File is empty." // add this to avoid an offset.
        ); 

        if ( $errnum != 4 ) 
            return "Upload Error! ".$upload_errors[ $errnum ];
	}
?>