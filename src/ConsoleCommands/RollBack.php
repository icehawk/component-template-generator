<?php
/**
 * @author hollodotme
 */

namespace IceHawk\ComponentTemplateGenerator\ConsoleCommands;

use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RollBack
 * @package IceHawk\ComponentTemplateGenerator\ConsoleCommands
 */
final class RollBack extends Command
{
	protected function configure()
	{
		$this->setDescription( 'Rolls back this PHAR to the previous version.' );
	}

	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$logger  = new ConsoleLogger( $output );
		$updater = new Updater( null, false, Updater::STRATEGY_GITHUB );

		/** @var GithubStrategy $strategy */
		$strategy = $updater->getStrategy();

		$strategy->setPackageName( 'icehawk/component-template-generator' );
		$strategy->setPharName( 'icehawk-ctg.phar' );
		$strategy->setCurrentLocalVersion( '@package_version@' );

		if ( $updater->rollback() )
		{
			$logger->info( 'Roll back successful!' );
		}
		else
		{
			$logger->alert( 'Roll back failed.' );
		}

		return 0;
	}
}