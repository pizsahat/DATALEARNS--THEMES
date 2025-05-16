<?php

add_shortcode('page-banner-default', 'PageBannerDefault');

function PageBannerDefault()
{
    ob_start(); ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri("images/bg_home.jpg") ?>)"></div>
        <div class="container container--narrow ">
            <div class="skills-section">
                <div class="skills-content">
                    <h2>Tingkatkan Keterampilan <br /><span class="highlight">Big Data dan AI</span></h2>
                    <p>Jadilah bagian dari era AI dan pelajari cara menyelesaikan lebih banyak hal dengan terlibat dalam proyek interaktif, pelatihan mandiri, komunitas, dan banyak lagi.</p>
                </div>
                <div class="skills-image">
                    <img src="<?php echo get_theme_file_uri('/images/three-people.png') ?>" alt="Big Data and AI Image" />
                </div>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}

add_shortcode('introduction-banner-default', 'IntroductionBannerDefault');

function IntroductionBannerDefault()
{
    ob_start(); ?>
    <div class="introduction-banner">
        <img src="<?php echo get_theme_file_uri('/images/Datalearns247-247-24th.jpg') ?>" alt="banner Big Data and AI Image" />
        <p>DataLearns247 is an educational program developed by Solusi247 which focuses on building Indonesian data talent through a curriculum based on experience in implementing big data and artificial intelligence</p>

    </div>
<?php
    return ob_get_clean();
}

add_shortcode('home-course-default', 'HomeCourseDefault');

function HomeCourseDefault()
{
    ob_start(); ?>
    <div class="container-home course-section">
        <h1>Our Courses</h1>
        <div class="content-course">
            <p>Start learning and mastering knowledge using a comprehensive curriculum to improve your skills</p>
            <div class="course-container">

                <?php
                $homepageCourse = new WP_Query(array(
                    'posts_per_page' => 3,
                    'post_type' => 'course',
                    'orderby' => 'rand'
                ));

                while ($homepageCourse->have_posts()) {
                    $homepageCourse->the_post();
                    get_template_part('template-parts/item', 'course');
                }
                ?>
            </div>

        </div>
    </div>
<?php
    return ob_get_clean();
}

add_shortcode('home-content-default', 'HomeContentDefault');

function HomeContentDefault()
{
    ob_start(); ?>
    <div class="container-home">
        <h1>OUR ARTICLES</h1>
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

add_shortcode('home-promo-default', 'HomePromoDefault');

function HomePromoDefault()
{
    ob_start(); ?>
    <div class="container-home promo-section">
        <img src="<?php echo get_theme_file_uri('/images/let-collaborate--jul-01.png') ?>" alt="">
        <div class="promo-description">
            <h1>ACADEMIC PARTNERSHIP</h1>
            <p>Helping the education institution in preparing students with the latest knowledge of Big Data and Artificial Intelligence to make them ready to enter the industrial world</p>
        </div>
    </div>
<?php
    return ob_get_clean();
}
