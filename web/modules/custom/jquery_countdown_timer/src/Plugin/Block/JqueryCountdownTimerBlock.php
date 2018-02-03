<?php
/**
 * @file
 * Contains \Drupal\jquery_countdown_timer\Plugin\Block\JqueryCountdownTimerBlock.
 */

namespace Drupal\jquery_countdown_timer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use \DateTime;

/**
 * Provides a "Jquery Countdown Timer" block.
 *
 * @Block(
 *   id = "jquery_countdown_timer",
 *   admin_label = @Translation("Countdown Timer")
 * )
 */
class JqueryCountdownTimerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $font_size = 16;
    $dt = new DateTime('tomorrow');
    $countdown_datetime = $dt->format('Y-m-d H:i:s');

    return array('countdown_datetime' => $countdown_datetime, 'font_size' => $font_size);
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['jquery_countdown_timer_date'] = array(
      '#type' => 'datetime',
      '#title' => t('Timer date'),
      '#required' => 1,
      '#default_value' => new DrupalDateTime($this->configuration['countdown_datetime']),
      '#date_date_element' => 'date',
      '#date_time_element' => 'time',
      '#date_year_range' => '2016:+50',
    );
    $form['jquery_countdown_timer_font_size'] = array(
      '#type' => 'number',
      '#title' => t('Timer font size'),
      '#default_value' => $this->configuration['font_size'],
      '#required' => 1
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $dt = $form_state->getValue('jquery_countdown_timer_date');
    $this->configuration['countdown_datetime'] = $dt->format('Y-m-d H:i:s');
    $this->configuration['font_size'] = $form_state->getValue('jquery_countdown_timer_font_size');
    $block_id = $form['id']['#default_value'];
    $this->configuration['block_id'] = $block_id;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $block_id = $this->configuration['block_id'];
    $settings = array(
      'unixtimestamp' => strtotime($this->configuration['countdown_datetime']),
      'fontsize' => $this->configuration['font_size'],
    );

    $build = array();
    $build['content'] = array(
      '#markup' => '<div id="jquery-countdown-timer-' . $block_id . '"></div><div id="jquery-countdown-timer-note-' . $block_id . '"></div>',
    );

    // Attach library containing css and js files.
    $build['#attached']['library'][] = 'jquery_countdown_timer/countdown.timer';
    $build['#attached']['drupalSettings']['countdown'][$block_id] = $settings;

    return $build;
  }
}
?>
