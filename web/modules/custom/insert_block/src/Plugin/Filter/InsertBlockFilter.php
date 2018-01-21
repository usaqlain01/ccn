<?php

namespace Drupal\insert_block\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\FilterProcessResult;

/**
 * Class InsertBlockFilter
 *
 * Inserts blocks into the content
 *
 * @package Drupal\insert_block\Plugin\Filter
 *
 * @Filter(
 *   id = "filter_insert_block",
 *   title = @Translation("Insert blocks"),
 *   description = @Translation("Inserts the contents of a block into a node using [block:block-entity-id] tags."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 *   settings = {
 *     "check_roles" = TRUE
 *   }
 * )
 */

class InsertBlockFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['check_roles'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Check roles permissions.'),
      '#default_value' => $this->settings['check_roles'],
      '#description' => $this->t('If user does not have permissions to view block it will be hidden.'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {

    if (preg_match_all("/\[block:([^\]]+)+\]/", $text, $match)) {
      // @todo implement role restrictions.
      $raw_tags = $repl = array();
      foreach ($match[1] as $key => $value) {
        $raw_tags[] = $match[0][$key];
        $block_id = $match[1][$key];

        $replacement = '';
        if ($block = \Drupal::service('entity.manager')->getStorage('block')->load($block_id)) {
          $block_view = \Drupal::service('entity.manager')
            ->getViewBuilder('block')
            ->view($block);
          $replacement = \Drupal::service('renderer')->render($block_view);
        }

        $repl[] = $replacement;
      }
      $text =  str_replace($raw_tags, $repl, $text);
    }

    return new FilterProcessResult($text);

  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    if ($long) {
      return t('<a name="filter-insert_block"></a>You may use [block:<em>block_entity_id</em>] tags to display the contents of block. To discover block entity id, visit admin/structure/block and hover over a block\'s configure link and look in your browser\'s status bar. The last "word" you see is the block ID.');
    }
    else {
      $tips_url = \Drupal\Core\Url::fromRoute("filter.tips_all", array(), array('fragment' => 'filter-insert_block'));
      return t('You may use <a href="@insert_block_help">[block:<em>block_entity_id</em>] tags</a> to display the contents of block.',
        array("@insert_block_help" => $tips_url->toString()));
    }
  }

}