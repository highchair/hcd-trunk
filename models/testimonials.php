<?php
class Testimonials extends ModelBase
{
	/*
     * Columns: id, display_name, slug, content, attribution, is_featured
     */
	function FindAll( $orderby='display_name ASC' )
	{
		return MyActiveRecord::FindAll('Testimonials', null, $orderby );
	}
	
	function FindById ($id) 
	{
		return MyActiveRecord::FindById('Testimonials', $id);
	}
	
	function FindByName( $name )
	{
		return array_shift(MyActiveRecord::FindBySql('Testimonials', "SELECT t.* FROM testimonials t WHERE t.slug like '" . $name . "'"));
	}
	
	function FindFeatured( $orderby = 'id DESC' )
	{
		return MyActiveRecord::FindBySql('Testimonials', "SELECT * FROM testimonials WHERE is_featured = true ORDER BY id DESC");
	}
	
	/* One function to control markup display preferences */
	function displayTestimonial( $float='' ) 
	{
		$attribution = ( ! empty($this->attribution) ) ? '<figcaption class="testimonial--attribution"><cite>'.$this->attribution.'</cite></figcaption>' : ''; 
		return '<figure class="testimonial testimonial__'.$float.'"><blockquote class="testimonial--content">'.$this->content.'</blockquote>'.$attribution.'</figure>';
	}	
}
?>