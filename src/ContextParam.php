<?php

namespace Drupal\alias_subpaths;

/**
 * Represents a context parameter used in alias subpaths.
 *
 * This class encapsulates a raw value, its processed value, and an optional
 * parameter name. It is used to manage context-specific data during
 * URL alias resolution.
 */
class ContextParam {

  /**
   * The original raw value of the parameter.
   *
   * @var string
   */
  private string $rawValue;

  /**
   * The processed value of the parameter.
   *
   * @var mixed
   */
  private mixed $processedValue = NULL;

  /**
   * The name of the parameter after processing.
   *
   * @var string|null
   */
  private ?string $paramName = NULL;

  /**
   * Constructs a new ContextParam object.
   *
   * @param string $rawValue
   *   The raw value of the context parameter.
   */
  public function __construct(string $rawValue) {
    $this->rawValue = $rawValue;
  }

  /**
   * Gets the raw value of the context parameter.
   *
   * @return string
   *   The raw value.
   */
  public function getRawValue(): string {
    return $this->rawValue;
  }

  /**
   * Sets the raw value of the context parameter.
   *
   * @param string $rawValue
   *   The new raw value.
   *
   * @return \Drupal\alias_subpaths\ContextParam
   *   The updated context parameter.
   */
  public function setRawValue(string $rawValue): ContextParam {
    $this->rawValue = $rawValue;
    return $this;
  }

  /**
   * Gets the processed value of the context parameter.
   *
   * @return mixed
   *   The processed value.
   */
  public function getProcessedValue(): mixed {
    return $this->processedValue;
  }

  /**
   * Sets the processed value of the context parameter.
   *
   * @param mixed $processedValue
   *   The new processed value.
   *
   * @return \Drupal\alias_subpaths\ContextParam
   *   The updated context parameter.
   */
  public function setProcessedValue(mixed $processedValue): ContextParam {
    $this->processedValue = $processedValue;
    return $this;
  }

  /**
   * Gets the parameter name associated with the context parameter.
   *
   * @return string|null
   *   The parameter name, or NULL if not set.
   */
  public function getParamName(): ?string {
    return $this->paramName;
  }

  /**
   * Sets the parameter name for the context parameter.
   *
   * @param string|null $paramName
   *   The parameter name.
   *
   * @return \Drupal\alias_subpaths\ContextParam
   *   The updated context parameter.
   */
  public function setParamName(?string $paramName): ContextParam {
    $this->paramName = $paramName;
    return $this;
  }

}
