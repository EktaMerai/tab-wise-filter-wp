<?php /* Template Name: Education */ ?>
<?php get_header(); ?>
<!--popular pet care slider-->
        
        <section class="blog-section section-space">
            <div class="container">
			<div class="blog-content ">
                    <?php

                    $terms = get_terms( array(
                    'taxonomy' => 'category',
                    'hide_empty' => false
                    ) );
                    // $cat_id;
                    foreach ($terms as $term){
                        // print_r($term);
                        $allterm[] = '.'. $term->slug;
                        $cat_id[] =  $term->term_id;
                    }

                    ?>
                    <ul class="filter-menu nav nav-tabs">

                        <li class="nav-item active" data-filter="<?php echo implode( ', ', $allterm ); ?>" data-cat_id="<?php echo implode( ', ', $cat_id ); ?>" data-all="all">
                            <a href="javascript:;" class="nav-link">All Post</a>
                        </li>
                        <?php

                            $terms = get_terms('category');

                            foreach ($terms as $term){ ?>
							<li class="nav-item" data-filter=".<?php echo $term->slug; ?>" data-count="<?php echo $term->count; ?>" data-cat_id="<?php echo $term->term_id ?>" data-all="no">
                                <a href="javascript:;" class="nav-link"><?php echo $term->name; ?></a>
                            </li>

                            <?php
                            } 
                        ?>

                    </ul>
                </div>
                <div class="filtr-container grid ajax_portfolio"></div>
        <?php wp_reset_postdata(); ?>
        </section>
		<div class="ajax-loader">
			 <center>
				<img src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mobile/1.4.5/images/ajax-loader.gif" class="img-responsive" />
			</center>
		</div>
<?php get_footer(); ?>
<style>
.ajax-loader {
  visibility: hidden;
  background-color: rgba(255,255,255,0.9);
  position: absolute;
  z-index: 100 !important;
  width: 100%;
  height:100%;
  top:0;
  left:0;
  bottom:0;
}
.ajax-loader img {
  position: absolute;
  transform: translate(-50%, -50%);
  top:50%;
  left:50%;
    
}
.filtr-container {
    height: auto !important;
}

</style>
<script type="text/javascript">
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
     $(document).on("click",".portfolio_links",function() {
             var paged =  $(this).attr("data-paged");
			var filterValue = $(this).attr('data-filter');
			var count = $(this).attr('data-count');
			var cat_id = $(this).attr('data-cat_id');
			var all = $(this).attr('data-all');
            $(this).prev().removeClass('current');
         $.ajax({
            type: 'POST',
			 beforeSend: function(){
					$('.ajax-loader').css("visibility", "visible");
				},
            url: ajaxurl,
            data: { 'action': 'pagination_portfolio', paged:paged, filterValue:filterValue,count:count,cat_id:cat_id,all:all },
            success: function(res) {
               $('.ajax_portfolio').html(res);
            },
			complete: function(){
				$('.ajax-loader').css("visibility", "hidden");
		  }
          })
        });
		/*call ajax tab click*/
		$('.nav-item').click(function() {
			var filterValue = $(this).attr('data-filter');
			var count = $(this).attr('data-count');
			var cat_id = $(this).attr('data-cat_id');
			var all = $(this).attr('data-all');
			$('.grid').isotope({ filter: filterValue });
			$('.nav-item').removeClass('active');
			$(this).addClass('active');
			$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: { 'action': 'tab_filter', filterValue:filterValue,count:count,cat_id:cat_id,all:all },
				beforeSend: function(){
					$('.ajax-loader').css("visibility", "visible");
					$('body').css("overflow","hidden");
			   },
			   complete: function(){
				 $('.ajax-loader').css("visibility", "hidden");
				 $('body').css("overflow","auto");
			   },
				success: function(res) {
				console.log(res);
				$('.ajax_portfolio').html(res);
			}
		  })
		});
		/*end call ajax*/
		/* call all on page load*/
		$( document ).ready(function() {
			$('.nav-item.active').trigger("click");
		});
		
</script>