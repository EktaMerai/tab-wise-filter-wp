<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    $parenthandle = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version') // this only works if you have Version in the style header
    );
}

/************************************************************************
Disable the Gutenberg Editer
*************************************************************************/
add_filter('use_block_editor_for_post', '__return_false');

/************************************************************************
Disable the Gutenberg _widgets
 *************************************************************************/
add_filter('use_widgets_block_editor', '__return_false');

/** Call filter all**/
add_action( 'wp_ajax_tab_filter', 'tab_filter' );
add_action( 'wp_ajax_nopriv_tab_filter', 'tab_filter' );
function tab_filter()
{
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$filterValue = $_REQUEST['filterValue'];
	$count = $_REQUEST['count'];
	$cat_id = $_REQUEST['cat_id'];
	$all = $_REQUEST['all'];
	
	if($all == "all"){
		$args = array(
			'post_type' => 'post', 
			'order' => 'DESC', // order it show the ASC or DESC
			'posts_per_page' => 6,
			'paged'     => $paged,
			); 
		$the_query = new WP_Query( $args );
	}else{
		$args=array(
			'posts_per_page' => 6,    
			'post_type' => 'post',
			'tax_query' => array(
				array(
					'taxonomy' => 'category', 
					'field'    => 'id',
					'terms'    => $cat_id,
				),
			   ),
			 );
			 
		$the_query = new WP_Query( $args );
	}
	/* echo "Last SQL-Query: {$the_query->request}"; */
	 if ($the_query->have_posts()):
	   while ($the_query->have_posts()): $the_query->the_post();
				  
			$termsArray = get_the_terms($post->ID,'category');
			$termsSlug = "";
			$termsCount = "";

			foreach($termsArray as $term){
			  $termsSlug .= $term->slug;
			  $termsCount .= $term->count;
			}
		?>
			<div class="all_img <?php echo $termsSlug; ?>" data-cat="<?php echo $termsSlug; ?>" data-count="<?php echo $termsCount; ?>">
                    <div class="pet-slider-li d-flex">
                         <a href="<?php  the_permalink(); ?>" class="img_wrapper"> 
                            <img src="<?php echo get_the_post_thumbnail_url(); ?>" class="img-fluid" />
                        </a> 
                        <div class="blog-caption">
                            <div class="blog-name">
                                <span class="blog-span">Discover</span>
                                <span><?php echo get_the_author(); ?></span>
                            </div>
                            <a href="<?php the_permalink(); ?>">
                                <h3><?php the_title(); ?></h3>
                            </a>
                            <p><?php the_content(); ?></p>
                            <p class="blog-date">
                                <span><?php echo get_the_date('M j'); ?></span>
                                <span><?php echo do_shortcode('[rt_reading_time postfix="MIN READ" postfix_singular="MIN READ" ]'); ?></span>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
				 <section class="pagination_section">
                <div class="container">
                    <nav aria-label="Page navigation example">
                        <div class="pagination_wrap">
                            <ul class="pagination_main mt-4">
                                <div class="wp-pagenavi without_ajax_pagination" role="navigation">
                                    <? 
                                        $last = $the_query->max_num_pages;
                                        if ($last > 1){
                                            $current_page = $paged;
                                        }
                                        ?>
                                        <a class="portfolio_links page larger previouspostslink" <?php if($paged == 1){echo "style='display:none;'";}?>href="javascript:void(0);" data-paged=<?php echo $current_page-1; ?> data-filter="<?php echo $filterValue ?>" data-cat_id="<?php echo $cat_id; ?>" data-all="<?php echo $all; ?>"><i class="fas fa-arrow-left"></i></a>
                                        <?
                                        for($i=1; $i<=$last; $i++) {
                                        if ($i == $pagenum ) {
                                        ?>
                                        <a href="#" class="selected" ><?php echo $i ?></a>
                                        <?php
                                        } else { ?>
                                        <a class="portfolio_links page larger <?php if($i == $paged){echo "current";}?>" <?php if($last <= 1){echo "style='display:none;'";}?> href="javascript:void(0);" data-paged=<?php echo $i; ?> data-filter="<?php echo $filterValue ?>" data-cat_id="<?php echo $cat_id; ?>" data-all="<?php echo $all; ?>"><?php echo $i ?></a>
                                        <?}
                                        }
                                        ?>
                                        <a class="portfolio_links nextpostslink <?php if($i == $paged){echo "current";}?>" <?php if($last <= 1){echo "style='display:none;'";}?>href="javascript:void(0);" data-paged=<?php echo $current_page + 1; ?> data-filter="<?php echo $filterValue ?>" data-cat_id="<?php echo $cat_id; ?>" data-all="<?php echo $all; ?>"><i class="fas fa-arrow-right"></i></a>
                                </div>
                            </ul>
                        </div>
                    </nav>
                </div>
            </section>
			<?	wp_reset_postdata();
				   endif;
				    wp_die();
	
}
/** end call all filter**/
/** Ajax pagination **/
add_action( 'wp_ajax_pagination_portfolio', 'pagination_portfolio' );
add_action( 'wp_ajax_nopriv_pagination_portfolio', 'pagination_portfolio' );
function pagination_portfolio()
{
	
	$paged = $_REQUEST['paged'];
	$filterValue = $_REQUEST['filterValue'];
	$count = $_REQUEST['count'];
	$cat_id = $_REQUEST['cat_id'];
	$all = $_REQUEST['all'];
	
	if($all == "all"){
		$args = array(
			'post_type' => 'post', 
			'order' => 'DESC', 
			'posts_per_page' => 6,
			'paged'     => $paged,
			); 
		$the_query = new WP_Query( $args );
	}else{
		$args=array(
			'posts_per_page' => 6,    
			'post_type' => 'post',
			'paged'     => $paged,
			'tax_query' => array(
				array(
					'taxonomy' => 'category', 
					'field'    => 'id',
					'terms'    => $cat_id,
				),
			   ),
			 );
			 
		$the_query = new WP_Query( $args );
	}  
      ?>
                <?php if ($the_query->have_posts()): ?>
                    <?php while ($the_query->have_posts()): $the_query->the_post();
                  
                    $termsArray = get_the_terms($post->ID,'category');
                    $termsSlug = "";

                    foreach($termsArray as $term){
                      $termsSlug .= $term->slug;
                    }

            ?>
			<div class="all_img <?php echo $termsSlug; ?>" data-cat="<?php echo $termsSlug; ?>">
                <div class="pet-slider-li d-flex">
                     <a href="<?php  the_permalink(); ?>" class="img_wrapper"> 
                        <img src="<?php echo get_the_post_thumbnail_url(); ?>" class="img-fluid" />
                    </a> 
                    <div class="blog-caption">
                        <div class="blog-name">
                            <span class="blog-span">Discover</span>
                            <span><?php echo get_the_author(); ?></span>
                        </div>
                        <a href="<?php the_permalink(); ?>">
                            <h3><?php the_title(); ?></h3>
                        </a>
                        <p><?php the_content(); ?></p>
                        <p class="blog-date">
                            <span><?php echo get_the_date('M j'); ?></span>
                            <span><?php echo do_shortcode('[rt_reading_time postfix="MIN READ" postfix_singular="MIN READ" ]'); ?></span>
                        </p>
                    </div>
                </div>
            </div>
            
    <?php
      endwhile;
      wp_reset_postdata();
      endif;?>
      <section class="pagination_section">
                <div class="container">
                    <nav aria-label="Page navigation example">
                        <div class="pagination_wrap">
                            <ul class="pagination_main mt-4">
                                <div class="wp-pagenavi without_ajax_pagination" role="navigation">
                    <? 
                        $last = $the_query->max_num_pages;
                        if ($last > 1){
                            $current_page = $paged;
                        }
                        ?>
                        <a class="portfolio_links page larger previouspostslink" <?php if($paged == 1){echo "style='display:none;'";}?>href="javascript:void(0);" data-paged=<?php echo $current_page-1; ?> data-filter="<?php echo $filterValue ?>" data-cat_id="<?php echo $cat_id; ?>" data-all="<?php echo $all; ?>"><i class="fas fa-arrow-left"></i></a>
                        <?
                        for($i=1; $i<=$last; $i++) {
                        if ($i == $pagenum ) {
                        ?>
                        <a href="#" class="selected" ><?php echo $i ?></a>
                        <?php
                        } else { ?>
                        <a class="portfolio_links page larger <?php if($i == $paged){echo "current";}?>" <?php if($last <= 1){echo "style='display:none;'";}?> href="javascript:void(0);" data-paged=<?php echo $i; ?> data-filter="<?php echo $filterValue ?>" data-cat_id="<?php echo $cat_id; ?>" data-all="<?php echo $all; ?>"><?php echo $i ?></a>
                        <?}
                        }
                        ?>
                        <a class="portfolio_links nextpostslink" <?php if($last == $current_page){echo "style='display:none;'";}?>href="javascript:void(0);" data-paged=<?php echo $current_page + 1; ?> data-filter="<?php echo $filterValue ?>" data-cat_id="<?php echo $cat_id; ?>" data-all="<?php echo $all; ?>"><i class="fas fa-arrow-right"></i></a>
                </div>
            </ul>
        </div>
		</nav>
		</div>
		</section>
    </div>
      <?
      wp_die();
}
/** End ajax pagination**/