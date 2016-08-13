<?php
/**
 * @author hollodotme
 */

namespace IceHawk\ComponentTemplateGenerator\Generators;

use Psr\Log\LoggerAwareTrait;

/**
 * Class SkeletonCodeGenerator
 * @package IceHawk\ComponentTemplateGenerator\Generators
 */
final class SkeletonCodeGenerator
{
	use LoggerAwareTrait;

	/** @var SkeletonCodeGeneratorConfig */
	private $config;

	/** @var array */
	private $existingFiles;

	public function __construct( SkeletonCodeGeneratorConfig $config )
	{
		$this->config        = $config;
		$this->existingFiles = [ ];
	}

	public function generate()
	{
		$skeletonDir  = $this->config->getSkeletonDir();
		$targetDir    = rtrim( $this->config->getTargetDir(), DIRECTORY_SEPARATOR );
		$replacements = $this->config->getReplacements();
		$search       = array_keys( $replacements );
		$replace      = array_values( $replacements );

		$dirIterator = new \RecursiveDirectoryIterator(
			$skeletonDir, \RecursiveDirectoryIterator::SKIP_DOTS
		);

		$iterator = new \RecursiveIteratorIterator(
			$dirIterator, \RecursiveIteratorIterator::SELF_FIRST
		);

		$skeletonDirQuoted = preg_quote( $skeletonDir, '#' );

		/** @var \SplFileInfo $fileInfo */
		foreach ( $iterator as $fileInfo )
		{
			$pathName = preg_replace( "#^{$skeletonDirQuoted}#", $targetDir, $fileInfo->getPathname() );

			if ( $fileInfo->isFile() )
			{
				$directory = str_replace( $search, $replace, dirname( $pathName ) );
				$this->createDirectory( $directory );

				$targetFilePath = str_replace( $search, $replace, $pathName );

				$this->createFile( $targetFilePath, $fileInfo->getPathname(), $replacements );
			}
		}
	}

	private function createDirectory( string $directory )
	{
		if ( file_exists( $directory ) )
		{
			if ( !in_array( $directory, $this->existingFiles ) )
			{
				$this->logger->warning( 'Directory ' . $directory . ' already exists.' );
				$this->existingFiles[] = $directory;
			}
		}
		else
		{
			if ( !mkdir( $directory, 0755, true ) )
			{
				$this->logger->error( 'Could not create directory: ' . $directory );
			}
			else
			{
				$this->logger->info( 'Directory: ' . $directory . ' created.' );
				$this->existingFiles[] = $directory;
			}
		}
	}

	private function createFile( string $targetFilePath, string $sourceFilePath, array $replacements )
	{
		$forceOverride = $this->config->forceOverride();
		$fileExists    = file_exists( $targetFilePath );

		if ( $fileExists && !$forceOverride )
		{
			if ( !in_array( $targetFilePath, $this->existingFiles ) )
			{
				$this->logger->warning( 'File ' . $targetFilePath . ' already exists, skipping.' );
				$this->existingFiles[] = $targetFilePath;
			}

			return;
		}

		$fileContents = file_get_contents( $sourceFilePath );
		$fileContents = str_replace(
			array_keys( $replacements ),
			array_values( $replacements ),
			$fileContents
		);

		$result = file_put_contents( $targetFilePath, $fileContents );

		if ( $result === false )
		{
			if ( $fileExists )
			{
				$this->logger->error( 'Could not overwrite file ' . $targetFilePath );
			}
			else
			{
				$this->logger->error( 'Could not create file ' . $targetFilePath );
			}
		}
		else
		{
			if ( $fileExists )
			{
				$this->logger->warning( 'Overwritten file ' . $targetFilePath );
				$this->existingFiles[] = $targetFilePath;
			}
			else
			{
				$this->logger->info( 'Created file ' . $targetFilePath );
				$this->existingFiles[] = $targetFilePath;
			}
		}
	}
}
