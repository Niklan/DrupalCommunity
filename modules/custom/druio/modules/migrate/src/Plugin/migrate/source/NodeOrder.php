<?php

namespace Drupal\druio_migrate\Plugin\migrate\source;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "druio_order"
 * )
 */
class NodeOrder extends SqlBase {

  /**
   * {@inheritdoc}
   *
   * IMPORTANT! This method must return single row result, we can't use joins
   * here. Don't edit!
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n', [
        'nid',
        'type',
        'title',
        'uid',
        'status',
        'created',
        'changed',
      ])
      ->condition('n.type', 'order');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'nid' => $this->t('The primary identifier for a node'),
      'type' => $this->t('The node_type.type of this node'),
      'title' => $this->t('The title of this node, always treated as non-markup plain text'),
      'uid' => $this->t('The users.uid that owns this node; initially, this is the user that created id'),
      'status' => $this->t('Boolean indicating whether the node is published (visible to non-administrators)'),
      'created' => $this->t('The Unix timestamp when the node was created'),
      'changed' => $this->t('The Unix timestamp when the node was most recently saved.'),
      // Custom added fields.
      'body_value' => $this->t('The value of body field'),
      'body_format' => $this->t('The format of body field'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'n',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $nid = $row->getSourceProperty('nid');

    // Body field.
    $body_query = $this->select('field_data_body', 'b')
      ->fields('b', ['body_value', 'body_format'])
      ->condition('entity_type', 'node')
      ->condition('bundle', 'order')
      ->condition('entity_id', $nid)
      ->execute()
      ->fetch();
    $row->setSourceProperty('body_value', $body_query['body_value']);
    $row->setSourceProperty('body_format', $body_query['body_format']);

    // Status migration from terms to list of text.
    $order_statuses = [
      3420 => 'active',
      3421 => 'archive',
      3422 => 'complete',
      3423 => 'cancelled',
    ];
    $order_status_query = $this->select('field_data_field_order_status_term', 'os')
      ->fields('os', ['field_order_status_term_tid'])
      ->condition('entity_type', 'node')
      ->condition('bundle', 'order')
      ->condition('entity_id', $nid)
      ->execute()
      ->fetch();
    $order_status_tid = $order_status_query['field_order_status_term_tid'];
    $order_status = $order_statuses[$order_status_tid];
    $row->setSourceProperty('order_status', $order_status);

    return parent::prepareRow($row);
  }

}
