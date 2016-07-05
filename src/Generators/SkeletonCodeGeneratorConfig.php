<?php
/**
 * @author hollodotme
 */

namespace IceHawk\ComponentTemplateGenerator\Generators;

/**
 * Class SkeletonCodeGeneratorConfig
 * @package IceHawk\ComponentTemplateGenerator\Generators
 */
final class SkeletonCodeGeneratorConfig
{
	/** @var string */
	private $skeletonDir;

	/** @var string */
	private $targetDir;

	/** @var array */
	private $replacements;

	/** @var bool */
	private $forceOverwrite;

	public function __construct( string $skeletonDir, string $targetDir, array $replacements, bool $forceOverwrite )
	{
		$this->skeletonDir    = $skeletonDir;
		$this->targetDir      = $targetDir;
		$this->replacements   = $replacements;
		$this->forceOverwrite = $forceOverwrite;
	}

	public function getSkeletonDir() : string
	{
		return $this->skeletonDir;
	}

	public function getTargetDir() : string
	{
		return $this->targetDir;
	}

	public function getReplacements() : array
	{
		return $this->replacements;
	}

	public function forceOverride() : bool
	{
		return $this->forceOverwrite;
	}
}