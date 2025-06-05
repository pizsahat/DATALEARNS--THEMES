<?php

/**
 * The template for displaying search results
 */
get_header();
?>

<div class="site-container">
    <div class="search-results-site-container">
        <h1 class="search-results-title">
            <?php
            printf(
                esc_html__('Search Results for: %s', 'text-domain'),
                '<span class="search-query">' . get_search_query() . '</span>'
            );
            ?>
        </h1>

        <div class="search-form-container">
            <?php get_search_form(); ?>
        </div>

        <?php if (have_posts()) : ?>
            <div class="search-results-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('search-result-item'); ?>>
                        <h2 class="search-result-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="search-result-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        <div class="search-result-meta">
                            <span class="post-date"><?php echo get_the_date(); ?></span>
                            <span class="post-type"><?php echo get_post_type_object(get_post_type())->labels->singular_name; ?></span>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="search-pagination">
                <?php the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => __('&larr; Previous', 'text-domain'),
                    'next_text' => __('Next &rarr;', 'text-domain'),
                )); ?>
            </div>
        <?php else : ?>
            <div class="no-results">
                <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'text-domain'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .search-results-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
    }

    .search-results-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 1.5rem 0;
        text-align: center;
    }

    .search-query {
        color: #4299e1;
        font-style: italic;
    }

    .search-form-container {
        max-width: 500px;
        margin: 0 auto 3rem;
    }

    .search-form-container input[type="search"] {
        width: 100%;
        padding: 0.75rem 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        font-size: 1rem;
        transition: all 0.2s ease;
    }

    .search-form-container input[type="search"]:focus {
        outline: none;
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
    }

    .search-results-grid {
        display: grid;
        grid-gap: 2rem;
    }

    .search-result-item {
        padding: 1.5rem;
        border-radius: 0.5rem;
        background: white;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .search-result-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .search-result-title {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .search-result-title a {
        color: #2d3748;
        text-decoration: none;
    }

    .search-result-title a:hover {
        color: #4299e1;
    }

    .search-result-excerpt {
        color: #4a5568;
        margin-bottom: 0.75rem;
        line-height: 1.6;
    }

    .search-result-meta {
        font-size: 0.875rem;
        color: #718096;
        display: flex;
        gap: 1rem;
    }

    .search-pagination {
        margin-top: 3rem;
        display: flex;
        justify-content: center;
    }

    .no-results {
        text-align: center;
        padding: 2rem;
        color: #4a5568;
    }

    @media (max-width: 768px) {
        .search-results-title {
            font-size: 2rem;
        }
    }
</style>

<?php
get_footer();
