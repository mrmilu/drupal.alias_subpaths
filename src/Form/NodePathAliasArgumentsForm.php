<?php

namespace Drupal\path_alias_arg\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NodePathAliasArgumentsForm extends FormBase {

  protected $entityTypeBundleInfo;

  public function __construct(EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.bundle.info'));
  }

  public function getFormId() {
    return 'node_path_alias_arguments_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $node_type = NULL) {
    $entity_types = $this->getEntityTypeOptions();

    $config = \Drupal::config('path_alias_arg.settings');
    $saved_values = $config->get($node_type . '__allowed_argument_types') ?? [];

    if (!$form_state->has('node_path_alias_arguments_rows')) {
      $form_state->set('node_path_alias_arguments_rows', count($saved_values) > 0 ? count($saved_values) : 1);
    }
    $rows = $form_state->get('node_path_alias_arguments_rows');

    $form['node_path_alias_arguments_rows'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];
    $form['node_path_alias_arguments_rows']['node_type'] = [
      '#type' => 'hidden',
      '#value' => $node_type,
    ];

    for ($i = 0; $i < $rows; $i++) {
      $default_entity_type = isset($saved_values[$i]) ? explode('__', $saved_values[$i])[0] : NULL;
      $default_bundle = isset($saved_values[$i]) ? explode('__', $saved_values[$i])[1] : NULL;
      $form['node_path_alias_arguments_rows'][$i] = [
        '#type' => 'details',
        '#title' => 'Argument #' . $i,
        '#open' => TRUE,
      ];
      $form['node_path_alias_arguments_rows'][$i]['entity_type'] = [
        '#type' => 'select',
        '#title' => $this->t('Entity Type'),
        '#options' => $entity_types,
        '#empty_option' => $this->t('- Select entity type -'),
        '#default_value' => $default_entity_type,
        '#ajax' => [
          'callback' => '::updateBundleOptions',
          'event' => 'change',
          'wrapper' => 'bundle-select-wrapper-' . $i,
        ],
      ];

      $selected_entity_type = $form_state->getValue(['node_path_alias_arguments_rows', $i, 'entity_type']) ?? $default_entity_type;
      $form['node_path_alias_arguments_rows'][$i]['bundle'] = [
        '#type' => 'select',
        '#title' => $this->t('Bundle'),
        '#prefix' => '<div id="bundle-select-wrapper-' . $i . '">',
        '#suffix' => '</div>',
        '#options' => $this->getBundleOptions($selected_entity_type),
        '#empty_option' => $this->t('- Select bundle -'),
        '#default_value' => $default_bundle,
      ];
    }

    $form['add_row'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add another'),
      '#submit' => ['::addRow'],
      '#ajax' => [
        'callback' => '::rebuildForm',
        'wrapper' => 'node-path-alias-arguments-wrapper',
      ],
    ];

    if ($rows > 1) {
      $form['remove_row'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove last'),
        '#submit' => ['::removeRow'],
        '#ajax' => [
          'callback' => '::rebuildForm',
          'wrapper' => 'node-path-alias-arguments-wrapper',
        ],
      ];
    }

    $form['#prefix'] = '<div id="node-path-alias-arguments-wrapper">';
    $form['#suffix'] = '</div>';

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  public function updateBundleOptions(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $index = $triggering_element['#parents'][1];

    return $form['node_path_alias_arguments_rows'][$index]['bundle'];
  }


  protected function getEntityTypeOptions() {
    $entity_types = \Drupal::entityTypeManager()->getDefinitions();
    $options = [];
    foreach ($entity_types as $entity_type_id => $entity_type) {
      if ($entity_type->hasKey('bundle')) {
        $options[$entity_type_id] = $entity_type->getLabel();
      }
    }
    return $options;
  }

  protected function getBundleOptions($entity_type = NULL) {
    if ($entity_type && $this->entityTypeBundleInfo->getBundleInfo($entity_type) !== NULL) {
      $bundles = $this->entityTypeBundleInfo->getBundleInfo($entity_type);
      return array_map(fn($bundle) => $bundle['label'], $bundles);
    }
    return [];
  }

  public function addRow(array &$form, FormStateInterface $form_state) {
    $form_state->set('node_path_alias_arguments_rows', $form_state->get('node_path_alias_arguments_rows') + 1);
    $form_state->setRebuild(TRUE);
  }

  public function removeRow(array &$form, FormStateInterface $form_state) {
    $rows = $form_state->get('node_path_alias_arguments_rows');
    if ($rows > 1) {
      $form_state->set('node_path_alias_arguments_rows', $rows - 1);
    }
    $form_state->setRebuild(TRUE);
  }

  public function rebuildForm(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue('node_path_alias_arguments_rows');
    $node_type = $values['node_type'];
    unset($values['node_type']);
    $result = array_map(fn($row) => $row['entity_type'] . '__' . $row['bundle'], $values);
    \Drupal::configFactory()->getEditable('path_alias_arg.settings')
      ->set($node_type . '__allowed_argument_types', $result)
      ->save();
    $this->messenger()->addMessage($this->t('Configuration saved.'));
  }
}
