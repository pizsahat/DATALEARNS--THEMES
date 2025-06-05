<div class="container">
    <div class="error-404-container">
        <div class="error-404-content">
            <div class="error-404-graphic">
                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>

            <h1 class="error-404-title"><?php _e('Oops! Page Not Found', 'text-domain'); ?></h1>

            <p class="error-404-message"><?php _e("We can't seem to find what you're looking for. The page may have been moved or doesn't exist anymore.", 'text-domain'); ?></p>

            <div class="error-404-search">
                <?php get_search_form(); ?>
            </div>

            <div class="error-404-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="error-404-button">
                    <?php _e('Return to Homepage', 'text-domain'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/content')); ?>" class="error-404-button secondary">
                    <?php _e('Visit Our Content', 'text-domain'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .error-404-container {
        max-width: 680px;
        margin: 20px auto;
        text-align: center;
        padding: 40px;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .error-404-graphic {
        margin-bottom: 30px;
    }

    .error-404-graphic svg {
        width: 80px;
        height: 80px;
        color: #6366f1;
    }

    .error-404-title {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
        line-height: 1.3;
    }

    .error-404-message {
        font-size: 1.125rem;
        color: #6b7280;
        margin-bottom: 32px;
        line-height: 1.6;
    }

    .error-404-search {
        max-width: 500px;
        margin: 0 auto 32px;
    }

    .error-404-search .search-field {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .error-404-search .search-field:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .error-404-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .error-404-button {
        display: inline-block;
        padding: 12px 24px;
        background-color: #6366f1;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .error-404-button:hover {
        background-color: #4f46e5;
        transform: translateY(-2px);
    }

    .error-404-button.secondary {
        background-color: white;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .error-404-button.secondary:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
    }

    @media (max-width: 640px) {
        .error-404-container {
            padding: 30px 20px;
        }

        .error-404-title {
            font-size: 1.75rem;
        }

        .error-404-message {
            font-size: 1rem;
        }

        .error-404-actions {
            flex-direction: column;
            gap: 12px;
        }

        .error-404-button {
            width: 100%;
        }
    }
</style>