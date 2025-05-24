<?php

add_shortcode('list-course', 'ListCourse');
function ListCourse($atts)
{
    $atts = shortcode_atts(array(
        'item' => 3,
        'course_type' => '',
        'skill_level' => '',
        'search' => '',
        'random' => 'true'
    ), $atts);

    ob_start();

    // Build query args
    $args = array(
        'posts_per_page' => intval($atts['item']),
        'post_type' => 'course',
        'meta_query' => array()
    );

    // Add orderby random jika diperlukan
    if ($atts['random'] === 'true') {
        $args['orderby'] = 'rand';
    }

    // Filter berdasarkan course_type
    if (!empty($atts['course_type'])) {
        $args['meta_query'][] = array(
            'key' => 'course_type',
            'value' => format_course_type($atts['course_type']),
            'compare' => 'LIKE',
        );
    }

    // Filter berdasarkan skill_level
    if (!empty($atts['skill_level'])) {
        $args['meta_query'][] = array(
            'key' => 'skill_level',
            'value' => $atts['skill_level'],
            'compare' => 'LIKE',
        );
    }

    // Filter berdasarkan search query
    if (!empty($atts['search'])) {
        $args['s'] = $atts['search'];
    }

    $courseQuery = new WP_Query($args);

    if ($courseQuery->have_posts()) {
        echo '<div class="archive-course-container">';
        while ($courseQuery->have_posts()) {
            $courseQuery->the_post();
            get_template_part('template-parts/item', 'course');
        }
        echo '</div>';
    } else {
        echo '<p>No courses found.</p>';
    }

    wp_reset_postdata();
    return ob_get_clean();
}

add_shortcode('list-content', 'ListContent');

function ListContent()
{
    ob_start(); ?>
    <div class=" container-home">
        <div class="content">
            <div class="left">
                <?php
                $homepageArticle = new WP_Query(array(
                    'posts_per_page' => 1,
                    'post_type' => 'content',
                    'orderby' => 'rand',
                    'meta_query' => array(
                        array(
                            'key' => 'um_content_restriction',
                            'compare' => 'NOT EXISTS'
                        )
                    )
                ));

                while ($homepageArticle->have_posts()) {
                    $homepageArticle->the_post();
                ?>
                    <a href="<?php the_permalink() ?>">
                        <img src="<?php the_post_thumbnail_url() ?>" alt="">
                    </a>
                    <div class="card-article-detail">
                        <p><?php echo esc_html(get_the_author()) . " - " . esc_html(get_the_date()); ?></p>
                        <a href="<?php the_permalink() ?>">
                            <h2><?php the_title() ?></h2>
                        </a>
                        <hr>
                        <?php
                        $group = get_field('content_group');

                        if ($group && is_array($group)) {
                            $group_titles = array();

                            foreach ($group as $group_id) {
                                $term = get_term($group_id);

                                if ($term && !is_wp_error($term)) {
                                    $group_titles[] = $term->name;
                                } else {
                                    $group_titles[] = 'Uncategorized';
                                }
                            }

                            $group_title = implode(', ', $group_titles);

                            echo '<span class="block-category">' . esc_html($group_title) . '</span>';
                        } else {
                            echo '<span class="block-category">Uncategorized</span>';
                        }
                        ?>
                    </div>
                <?php
                }
                wp_reset_postdata();
                ?>

            </div>
            <div class="right">
                <?php
                $articles = new WP_Query(array(
                    'posts_per_page' => 5,
                    'post_type' => 'content',
                    'orderby' => 'date',
                    'order' => 'ASC'
                ));
                $count = 1;
                while ($articles->have_posts()) {
                    $articles->the_post(); ?>
                    <div class="article-item">
                        <div class="article-number"><?php echo str_pad($count, 2, '0', STR_PAD_LEFT); ?></div>
                        <div class="article-content">
                            <?php

                            $group = get_field('content_group');

                            if ($group && is_array($group)) {
                                $group_titles = array();

                                foreach ($group as $group_id) {
                                    $term = get_term($group_id);

                                    if ($term && !is_wp_error($term)) {
                                        $group_titles[] = $term->name;
                                    } else {
                                        $group_titles[] = 'Uncategorized';
                                    }
                                }

                                $group_title = implode(', ', $group_titles);

                                echo '<span class="article-type">' . esc_html($group_title) . '</span>';
                            } else {
                                echo '<span class="article-type">Uncategorized</span>';
                            }

                            ?>
                            <br>
                            <a href="<?php the_permalink(); ?>" class="article-title"><?php the_title(); ?></a>
                        </div>
                    </div>
                    <?php $count++; ?>
                <?php }
                wp_reset_postdata(); ?>
            </div>
        </div>

    </div>
<?php
    return ob_get_clean();
}
