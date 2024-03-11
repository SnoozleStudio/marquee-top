<?php
/**
 * Marquee Top
 *
 * @package   MarqueeTop
 * @author    Paolo Mason
 * @copyright 2024 Paolo Mason
 * @license   GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Marquee Top
 * Description:       Add marquee at the top header.
 * Version:           1.0.3
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Snoozle Studio
 * Author URI:        https://snoozle.studio
 * Text Domain:       ssmt
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

// Enqueue scripts and styles
function ssmt_enqueue_scripts()
{
  wp_enqueue_style('ssmt-style', plugins_url('style.css', __FILE__));
  wp_enqueue_script('ssmt-marquee3000', plugins_url('marquee3k.js', __FILE__), array('jquery'), '1.0', true);
  wp_enqueue_script('ssmt-script', plugins_url('script.js', __FILE__), array('ssmt-marquee3000'), '1.0.3', true);
}
add_action('wp_enqueue_scripts', 'ssmt_enqueue_scripts');

// Add settings menu
function ssmt_add_menu()
{
  add_menu_page('Marquee Top Settings', 'Marquee Top', 'manage_options', 'ssmt_settings', 'ssmt_render_settings_page');
}
add_action('admin_menu', 'ssmt_add_menu');

// Settings page callback
function ssmt_render_settings_page()
{
  ?>
  <div class="wrap">
    <h1>Marquee Top Settings</h1>
    <form method="post" action="options.php">
      <?php
      settings_fields('ssmt_settings_group');
      do_settings_sections('ssmt_settings_section');
      submit_button();
      ?>
    </form>
  </div>
  <?php
}

// Register and initialize settings
function ssmt_register_settings()
{
  register_setting('ssmt_settings_group', 'ssmt_marquee_text', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => ''));
  register_setting('ssmt_settings_group', 'ssmt_marquee_speed', array('type' => 'float', 'sanitize_callback' => 'sanitize_text_field', 'default' => 0.55));
  register_setting('ssmt_settings_group', 'ssmt_marquee_url', array('type' => 'string', 'sanitize_callback' => 'esc_url', 'default' => ''));
  register_setting('ssmt_settings_group', 'ssmt_marquee_bg_color', array('type' => 'string', 'sanitize_callback' => 'sanitize_hex_color', 'default' => ''));
  register_setting('ssmt_settings_group', 'ssmt_span_color', array('type' => 'string', 'sanitize_callback' => 'sanitize_hex_color', 'default' => ''));

  add_settings_section('ssmt_settings_section', 'Marquee Settings', 'ssmt_render_section_callback', 'ssmt_settings_section');

  add_settings_field('ssmt_marquee_text', 'Marquee Text', 'ssmt_render_text_field', 'ssmt_settings_section', 'ssmt_settings_section');
  add_settings_field('ssmt_marquee_speed', 'Marquee Speed', 'ssmt_render_speed_field', 'ssmt_settings_section', 'ssmt_settings_section');
  add_settings_field('ssmt_marquee_url', 'Marquee URL', 'ssmt_render_url_field', 'ssmt_settings_section', 'ssmt_settings_section');
  add_settings_field('ssmt_marquee_bg_color', 'Marquee Background Color', 'ssmt_render_bg_color_field', 'ssmt_settings_section', 'ssmt_settings_section');
  add_settings_field('ssmt_span_color', 'Span Color', 'ssmt_render_span_color_field', 'ssmt_settings_section', 'ssmt_settings_section');
}

add_action('admin_init', 'ssmt_register_settings');

// Section callback
function ssmt_render_section_callback()
{
  echo '<p>Marquee top by <strong>Snoozle</strong>Studio.</p>';
}

// Text field callback
function ssmt_render_text_field()
{
  $text = get_option('ssmt_marquee_text');
  echo '<input type="text" name="ssmt_marquee_text" value="' . esc_attr($text) . '" />';
}

// Speed field callback
function ssmt_render_speed_field()
{
  $speed = get_option('ssmt_marquee_speed', 0.55);
  echo '<input type="number" step="0.01" name="ssmt_marquee_speed" value="' . esc_attr($speed) . '" />';
}

// URL field callback
function ssmt_render_url_field()
{
  $url = get_option('ssmt_marquee_url');
  echo '<input type="text" name="ssmt_marquee_url" value="' . esc_url($url) . '" />';
}

// Background Color field callback
function ssmt_render_bg_color_field()
{
  $bg_color = get_option('ssmt_marquee_bg_color');
  echo '<input type="text" name="ssmt_marquee_bg_color" value="' . esc_attr($bg_color) . '" />';
}

// Span Color field callback
function ssmt_render_span_color_field()
{
  $span_color = get_option('ssmt_span_color');
  echo '<input type="text" name="ssmt_span_color" value="' . esc_attr($span_color) . '" />';
}

// Marquee function
function ssmt_render_marquee()
{
  if (is_home() || is_front_page()) {
    $text = get_option('ssmt_marquee_text', '');
    $speed = floatval(get_option('ssmt_marquee_speed', 0.55));
    $url = esc_url(get_option('ssmt_marquee_url', ''));
    $bg_color = get_option('ssmt_marquee_bg_color');
    $span_color = get_option('ssmt_span_color');

    $marquee_style = !empty($bg_color) ? ' style="background-color: ' . esc_attr($bg_color) . ';"' : '';
    $span_style = !empty($span_color) ? ' style="color: ' . esc_attr($span_color) . ';"' : '';

    $marquee = '<div class="marquee3k" data-speed="' . esc_attr($speed) . '"' . $marquee_style . '><span' . $span_style . '>' . esc_html($text) . '</span></div>';
    if (!empty($url)) {
      $marquee = '<a href="' . esc_url($url) . '">' . $marquee . '</a>';
    }

    echo '<div class="container-marquee">' . wp_kses_post($marquee) . '</div>';
  }
}

add_action('wp_body_open', 'ssmt_render_marquee');
