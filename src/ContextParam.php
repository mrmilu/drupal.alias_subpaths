<?php

namespace Drupal\alias_subpaths;

class ContextParam {

  private string $rawValue;
  private mixed $processedValue = NULL;

  private ?string $paramName = NULL;

  /**
   * @param string $rawValue
   *
   * @return void
   */
  public function __construct(string $rawValue) {
    $this->rawValue = $rawValue;
  }

  public function getRawValue(): string {
    return $this->rawValue;
  }

  public function setRawValue(string $rawValue): ContextParam {
    $this->rawValue = $rawValue;
    return $this;
  }

  public function getProcessedValue(): mixed {
    return $this->processedValue;
  }

  public function setProcessedValue(mixed $processedValue): ContextParam {
    $this->processedValue = $processedValue;
    return $this;
  }

  public function getParamName(): ?string {
    return $this->paramName;
  }

  public function setParamName(?string $paramName): ContextParam {
    $this->paramName = $paramName;
    return $this;
  }

}
