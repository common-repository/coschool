<?php
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Student\Data as Student_Data;

global $course_data;

$reviews    = $course_data->get_reviews();
$rating     = $course_data->get( 'rating' );
$format     = get_option( 'links_updated_date_format' );

$sorted_rating = [ 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0 ];
foreach ( $reviews as $review ) {
    if ( $review['rating'] > 0 ) {
        $sorted_rating[ $review['rating'] ] ++;
    }
}

$_reviews = array_reverse( $reviews );

$reviews = [];
foreach ( $_reviews as $comment ) {

    $args  = [
        'id'                => $comment['id'],
        'course_id'         => $comment['course_id'],
        'comment_parent'    => $comment['comment_parent'],
        'reviewer_id'       => $comment['reviewer_id'],
        'reviewer_name'     => $comment['reviewer_name'],
        'reviewer_email'    => $comment['reviewer_email'],
        'content'           => $comment['content'],
        'rating'            => $comment['rating'],
        'time'              => $comment['time'] ,
    ];

    if ( empty( $comment['comment_parent'] ) ) {
        $reviews[ $comment['id'] ][] = $args;
    }
    else {
        $reviews[ $comment['comment_parent'] ][] = $args;
    }
}

?>
<div class="coschool-cs-review-section">
    <div class="coschool-csr-card">
    <div class="coschool-csr-card-left">
        <div class="coschool-csr-avg">
            <span class="coschool-csr-avg-pt"><?php esc_html_e( $rating ) ?></span><span class="coschool-csr-total-pt">/5</span>
        </div>
        <div class="coschool-csr-total"><?php printf( __( 'Based on %d reviews', 'coschool' ),  count( $_reviews )  ); ?></div>
        <div class="coschool-csr-stars"><?php echo coschool_populate_stars( $rating ); ?></div>
    </div>
    <div class="coschool-csr-card-right">
     <?php foreach ( [ 5, 4, 3, 2, 1 ] as $item ):
        $segment_count = $sorted_rating[ $item ];
        $segment_share = count( $_reviews ) > 0 ? $segment_count / count( $_reviews ) * 100 : 0;
        ?>
         <div class="coschool-csr-progressbar">
             <div class="coschool-csr-pbar-text"><?php echo coschool_populate_stars( $item ) . ' ' . sprintf( __( '(%d reviews)', 'coschool' ), $segment_count ); ?></div>
             <div class="coschool-csr-pbar">
                 <div class="coschool-csr-pbar-inner" style="width: <?php echo esc_attr( $segment_share ); ?>%;"></div>
             </div>
         </div>
     <?php endforeach; ?>
    </div>
    </div>

    <div class="coschool-csr-reviews">
        <h2 class="coschool-singular-title-sm"><?php _e( 'User Reviews', 'coschool' ); ?></h2>

        <?php foreach ( $reviews as $review_id => $_reviews ): 
            foreach ( $_reviews as $review ) :
                $reviewer = new Student_Data( $review['reviewer_id'] );

                $class = ' children';
                if ( $review_id == $review['id'] ) {
                   $class = ' parent';
                }
                ?>
                
                <div class="coschool-csr-review<?php esc_attr_e( $class ); ?>">
                    <div class="coschool-csr-review-header">
                        <div class="coschool-csr-review-author-img"><img src="<?php echo esc_url( $reviewer->get_avatar_url() ); ?>" alt="<?php esc_attr_e( $reviewer->get( 'name' ) ); ?>"></div>
                        <div class="coschool-csr-review-author-name"><?php esc_html_e( $reviewer->get( 'name' ) ); ?></div>
                        <div class="coschool-csr-review-time"><?php echo date( $format, $review['time'] ) ?></div>
                        <?php if ( $review['comment_parent'] == 0 ): ?>
                            <div class="coschool-csr-review-star"><?php echo coschool_populate_stars( $review['rating'] ); ?></div>
                        <?php endif ?>
                    </div>
                    <div class="coschool-csr-review-content">"<?php echo wp_kses_post( $review['content'] ); ?>"</div>
                </div>
                <?php
            endforeach;
        endforeach; ?>
    </div>
</div>