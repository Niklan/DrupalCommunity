<?php

namespace Drupal\druio_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "druio_node_project"
 * )
 */
class NodeProject extends SqlBase {

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
      ->condition('n.type', 'project');
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
    return parent::prepareRow($row);
  }

}
