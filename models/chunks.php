<?php
class Chunks extends ModelBase
{
	/* Chunks: Simple, small blocks of content that don't deserve a page...
	 * 
	 */
	
	
	function FindAll()
	{
		/*
		 * Columns: id, slug, description, full_html (boolean), content
		 */
		return MyActiveRecord::FindAll('Chunks'); 
	}
	
	function FindById( $id="" )
	{
		// Double check the id to see if it is numeric? Does MyActiveRecord already do that? 
		return MyActiveRecord::FindById( 'Chunks', $id );
	}
	
	function FindBySlug( $slug="" )
	{
	    $slug = mysql_real_escape_string( $slug, MyActiveRecord::Connection() );
		return MyActiveRecord::FindFirst( 'Chunks', "slug = '{$slug}'" );
	} 
	
	function RenderChunk( $slug="", $cachelength="" ) 
	{
	    if( ! empty($slug) ) {
        	
        	// Returns a new query or a cached one
        	$cache_folder = SERVER_CACHE_ROOT; // This usually points to /htdocs/cache/ on Modwest servers
            
            if ( empty($cachelength) ) { $cachelength = 60 * 60 * 3; } // seconds * minutes * hours = cache for 3 hours
            
            $usecache = false;
            $cachefile = $cache_folder . $slug . '.html';
            $user = Users::GetCurrentUser(); 
            
            // Skip using a cached file is there is a user logged in right now
            if ( empty($user) ) {
                
                // Check if we have this request cached recently;
                if( file_exists( $cachefile ) ) {
                    
                    if( time() - filemtime( $cachefile ) <= $cachelength ) {    // Is this file recent enough to use? 
                        $cachecontents = file_get_contents( $cachefile ); // Return the cached xml
                        
                        if ( ! empty($cachecontents) ) {
                            return $cachecontents; 
                            $usecache = true; // not neccesary, as the return statement stops execution
                        }
                    } else unlink( $cachefile ); // File stale or empty, delete
                }
            }
            
            if( ! $usecache ) {
            
                $result = Chunks::FindBySlug( $slug ); 
                $result = image_display( $result->content );
        	    $result = email_display( $result );
                
                // Store our cache version
                file_put_contents( $cachefile, $result );
                return $result; 
            }
        } else { return null; }
	}
}
?>