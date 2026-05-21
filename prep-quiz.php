<?php

/**
 * Plugin Name: PrEP Eligibility Quiz
 * Description: An interactive PrEP eligibility screening tool. Use shortcode [prep_quiz] to display.
 * Version: 1.0.0
 * Author: ahfweb
 * License: GPL v2 or later
 * Text Domain: prep-quiz
 * Domain Path: /languages
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Enqueue plugin scripts and styles
 */
function prep_quiz_enqueue_assets()
{
    $plugin_url = plugin_dir_url(__FILE__);

    // Enqueue CSS
    wp_enqueue_style(
        'prep-quiz-style',
        $plugin_url . 'assets/css/prep-quiz.css',
        array(),
        '1.0.0'
    );

    // Enqueue JS
    wp_enqueue_script(
        'prep-quiz-script',
        $plugin_url . 'assets/js/prep-quiz.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'prep_quiz_enqueue_assets');

/**
 * Register the [prep_quiz] shortcode
 */
function prep_quiz_shortcode()
{
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
