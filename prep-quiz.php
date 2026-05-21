<?php

/**
 * Plugin Name: PrEP Eligibility Quiz
 * Description: An interactive PrEP eligibility screening tool. Use shortcode [prep_quiz] to display.
 * Version: 1.0.0
 * Author: ahfweb
 * License: GPL v2 or later
 * Text Domain: prep-quiz
 * Domain Path: /languages
 * Requires: 5.0
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

// Define plugin constants
if (! defined('PREP_QUIZ_VERSION')) {
    define('PREP_QUIZ_VERSION', '1.0.0');
}
if (! defined('PREP_QUIZ_PATH')) {
    define('PREP_QUIZ_PATH', plugin_dir_path(__FILE__));
}
if (! defined('PREP_QUIZ_URL')) {
    define('PREP_QUIZ_URL', plugin_dir_url(__FILE__));
}

// Security: Prevent direct file access
if (! function_exists('add_action')) {
    exit('No direct access allowed');
}

/**
 * Enqueue plugin scripts and styles
 */
function prep_quiz_enqueue_assets()
{
    // Enqueue CSS with version cache busting
    wp_enqueue_style(
        'prep-quiz-style',
        PREP_QUIZ_URL . 'assets/css/prep-quiz.css',
        array(),
        PREP_QUIZ_VERSION
    );

    // Enqueue JS with version cache busting
    wp_enqueue_script(
        'prep-quiz-script',
        PREP_QUIZ_URL . 'assets/js/prep-quiz.js',
        array(),
        PREP_QUIZ_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'prep_quiz_enqueue_assets');

/**
 * Register the [prep_quiz] shortcode
 */
function prep_quiz_shortcode()
{
    // Return empty if user doesn't have permission to view
    if (! is_user_logged_in() && ! current_user_can('read')) {
        // Allow anonymous users for public quiz
        // For restricted access, check permissions here
    }

    ob_start();
?>

    <div id="prep-quiz" role="main" aria-label="PrEP Eligibility Quiz">

        <div class="pq-progress" role="progressbar" aria-label="Quiz progress" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            <div class="pq-progress-fill" id="pq-prog-fill" style="width:0%"></div>
        </div>

        <!-- ── QUESTION ── -->
        <div class="pq-screen" id="pq-question">
            <div class="pq-q-header">
                <div class="pq-q-num" id="pq-num" aria-hidden="true">1</div>
                <p class="pq-q-text" id="pq-text"></p>
            </div>
            <div class="pq-options" id="pq-opts" role="radiogroup" aria-label="Select an answer"></div>
            <div class="pq-btn-row">
                <div class="pq-btn-controls">
                    <button class="pq-btn-back" id="pq-btn-back" aria-label="Go to previous question">← Back</button>
                    <button class="pq-btn-ok" id="pq-btn-ok" disabled aria-label="Confirm answer">OK</button>
                </div>
                <!-- <div class="pq-kbd-hint" aria-hidden="true">
                    <span class="pq-kbd">A</span>
                    <span class="pq-kbd">B</span>
                    <span class="pq-kbd">C</span>
                    <span class="pq-hint-text">to select &nbsp;&bull;&nbsp;</span>
                    <span class="pq-kbd">Enter</span>
                    <span class="pq-hint-text">to confirm</span>
                </div> -->
            </div>
        </div>

        <!-- ── RESULT ── -->
        <div class="pq-screen" id="pq-result">
            <span class="pq-result-tag">Your Result</span>
            <h2 class="pq-result-title" id="pq-res-title"></h2>
            <div class="pq-divider"></div>
            <div class="pq-result-body" id="pq-res-body"></div>
            <button class="pq-btn-primary" id="pq-btn-retake">Retake Quiz</button>
        </div>

    </div><!-- #prep-quiz -->

<?php
    return ob_get_clean();
}
add_shortcode('prep_quiz', 'prep_quiz_shortcode');

/**
 * Add security headers
 */
function prep_quiz_security_headers()
{
    if (! is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
    }
}
add_action('send_headers', 'prep_quiz_security_headers');

/**
 * Plugin activation hook
 */
function prep_quiz_activate()
{
    // Log activation for debugging
    error_log('PrEP Quiz Plugin activated on ' . current_time('mysql'));
}
register_activation_hook(__FILE__, 'prep_quiz_activate');

/**
 * Plugin deactivation hook
 */
function prep_quiz_deactivate()
{
    // Cleanup if needed
    error_log('PrEP Quiz Plugin deactivated on ' . current_time('mysql'));
}
register_deactivation_hook(__FILE__, 'prep_quiz_deactivate');
