<?php
/**
 * @author hollodotme
 */

namespace IceHawk\ComponentTemplateGenerator\ConsoleCommands;

use IceHawk\ComponentTemplateGenerator\Generators\SkeletonCodeGenerator;
use IceHawk\ComponentTemplateGenerator\Generators\SkeletonCodeGeneratorConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class GenerateComponent
 * @package IceHawk\ComponentTemplateGenerator\ConsoleCommands
 */
final class GenerateComponent extends Command
{
	protected function configure()
	{
		$this->setDescription( 'Generates a new IceHawk component template' );

		$this->addArgument( 'targetDir', InputArgument::OPTIONAL, 'Specifies the target directory', WORKING_DIR );
		$this->addOption( 'force', 'f', InputOption::VALUE_NONE, 'Forces overwrite of files' );
	}

	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$logger = new ConsoleLogger( $output );

		/** @var QuestionHelper $questionHelper */
		$questionHelper = $this->getHelper( 'question' );

		$vendorNameQuestion    = $this->getQuestion( "* Vendor name", 'IceHawk' );
		$componentNameQuestion = $this->getQuestion( "* Component name", 'NewComponent' );
		$descriptionQuestion   = $this->getQuestion( "* Description", 'A new IceHawk component' );

		$vendorName    = $questionHelper->ask( $input, $output, $vendorNameQuestion );
		$componentName = $questionHelper->ask( $input, $output, $componentNameQuestion );
		$description   = $questionHelper->ask( $input, $output, $descriptionQuestion );

		$suggestedComposerName = strtolower( $vendorName ) . '/' . $this->uncamelize( $componentName );
		$composerNameQuestion  = $this->getQuestion( '* Composer name: ', $suggestedComposerName );
		$composerName          = $questionHelper->ask( $input, $output, $composerNameQuestion );

		$suggestedNamespace = $vendorName . '\\' . $componentName;
		$namespaceQuestion  = $this->getQuestion( '* Root namespace: ', $suggestedNamespace );
		$namespace          = $questionHelper->ask( $input, $output, $namespaceQuestion );

		$forceOverwrite = (bool)$input->getOption( 'force' );
		$skeletonDir    = PHAR_DIR . '/src/CodeTemplates/Component';
		$targetDir      = $input->getArgument( 'targetDir' ) . DIRECTORY_SEPARATOR . $componentName;
		$replacements   = [
			'__NAMESPACE__'         => $namespace,
			'__NAMESPACE_ESCAPED__' => addcslashes( $namespace, '\\' ),
			'__VENDOR_NAME__'       => $vendorName,
			'__COMPOSER_NAME__'     => $composerName,
			'__COMPONENT_NAME__'    => $componentName,
			'__DESCRIPTION__'       => $description,
			'__AUTHOR__'            => posix_getlogin(),
		];

		$generatorConfig = new SkeletonCodeGeneratorConfig( $skeletonDir, $targetDir, $replacements, $forceOverwrite );
		$generator       = new SkeletonCodeGenerator( $generatorConfig );

		$generator->setLogger( $logger );
		$generator->generate();

		return 0;
	}

	private function getQuestion( string $question, string $default = null ) : Question
	{
		$questionString = sprintf( "\n%s%s: ", $question, $default ? " (Default: \e[33m{$default}\e[0m)" : '' );

		return new Question( $questionString, $default );
	}

	private function uncamelize( string $camel, string $splitter = '-' ) : string
	{
		$camel = preg_replace(
			'#(?!^)[[:upper:]][[:lower:]]#', '$0',
			preg_replace( '#(?!^)[[:upper:]]+#', $splitter . '$0', $camel )
		);

		return strtolower( $camel );
	}

}