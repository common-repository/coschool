<?php 
global $lesson_data;

$discussions = $lesson_data->get( 'discussion' );
?>

<div id="comments" class="comments-area coschool-comments-area">
 
        <h2 class="comments-title">
            <?php
                printf( _nx( 'One comment on "%2$s"', '%1$s Comments on "%2$s"', get_comments_number(), 'comments title', 'twentythirteen' ),
                    number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
            ?>
        </h2>
        <ol class="comment-list coschool-comment-list">
        <?php
		echo wp_list_comments( [ 'style' => 'ol', 'callback' => function ( $comment, $args, $depth ) {
			$comment_id 		= get_comment_ID();
		    $comment_post_id 	= $comment->comment_post_ID;
		    
		    if ( 'div' === $args['style'] ) {
		        $tag       = 'div';
		        $add_below = 'comment';
		    } else {
		        $tag       = 'li';
		        $add_below = 'div-comment';
		    }
		    ?>

		    <<?php echo $tag; ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID() ?>"><?php 
		    if ( 'div' != $args['style'] ) { ?>
		        <div id="comment-<?php esc_attr( comment_ID() ); ?>" class="coschool-comments"><?php
		    } ?>

    		<div class="coschool-comment-avatar">
    			<?php echo "<img src='" . esc_url( get_avatar_url( $comment->user_id ) ) . "'/>";  ?>
    		</div>
    		<div class="coschool-comment-content">
    			<div class="coschool-comment-by">
    				<strong class="author"><?php echo get_comment_author_link( $comment_id  ); ?></strong>
    				<span class="date float-right"><?php printf( esc_html__('%1$s at %2$s' , 'coschool'), get_comment_time(), get_comment_date() ); ?></span>
    			</div>
    			<div class="comment-text"><?php comment_text(); ?></div>
    			<div class="comment-reply"><a href="#"><i class="fa fa-reply"></i> <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?></a></div>
    		</div>
		    <?php 
		    if ( 'div' != $args['style'] ) : ?>
		        </div><?php 
		    endif;

		} ], $discussions, 5 );

		?>

		</ol><!-- .comment-list -->

    <?php comment_form( [], $lesson_data->get( 'id' ) ); ?>
 
</div><!-- #comments -->