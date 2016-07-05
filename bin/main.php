<?php declare(strict_types = 1);

/**
 * @author hollodotme
 */

namespace IceHawk\ComponentTemplateGenerator;

use IceHawk\ComponentTemplateGenerator\ConsoleCommands\GenerateComponent;
use IceHawk\ComponentTemplateGenerator\ConsoleCommands\RollBack;
use IceHawk\ComponentTemplateGenerator\ConsoleCommands\SelfUpdate;
use Symfony\Component\Console\Application;

error_reporting( -1 );
ini_set( 'display_errors', 'On' );

require(__DIR__ . '/../vendor/autoload.php');

define( 'PHAR_DIR', dirname( __DIR__ ) );
define( 'WORKING_DIR', getcwd() );

try
{
	$app = new Application( 'IceHawk CTG', '@package_version@' );

	$app->addCommands(
		[
			new GenerateComponent( 'generate:component' ),
			new SelfUpdate( 'self-update' ),
			new RollBack( 'rollback' ),
		]
	);

	$code = $app->run();

	exit($code);
}
catch ( \Throwable $e )
{
	echo "Uncaught " . get_class( $e ) . " with message: " . $e->getMessage() . "\n";
	echo $e->getTraceAsString();

	exit(1);
}