<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\ScrumCalendar\Bundle\ScrumCalendarBundle\DependencyInjection;

use DateInterval;
use DateTime;
use Endroid\ScrumCalendar\SprintDefinition;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class EndroidScrumCalendarExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $sprintManagerDefinition = $container->getDefinition('endroid_scrum_calendar.sprint_definition_registry');

        foreach ($config['sprint_definitions'] as $name => $sprint) {
            $dateStartDefinition = new Definition(DateTime::class);
            $dateStartDefinition->addArgument($sprint['date_start']);
            $dateIntervalDefinition = new Definition(DateInterval::class);
            $dateIntervalDefinition->addArgument($sprint['date_interval']);
            $sprintDefinition = new Definition(SprintDefinition::class);
            $sprintDefinition->setArguments([$sprint['label'], $sprint['url'], $dateStartDefinition, $dateIntervalDefinition, $sprint['repeat']]);
            $sprintManagerDefinition->addMethodCall('set', [$name, $sprintDefinition]);
        }
    }
}
