<?php

namespace Sp\BowerBundle\EventListener;

use Sp\BowerBundle\Bower\BowerEvent;
use Sp\BowerBundle\Bower\BowerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class ExecListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param \Sp\BowerBundle\Bower\BowerEvent $event
     */
    public function onPreExec(BowerEvent $event)
    {
        if ($event->getConfiguration()->getDirectory() == $this->cacheDir) {
            return;
        }

        $config = $event->getConfiguration();

        $tmpConfig = clone $config;
        $tmpConfig->setDirectory($this->cacheDir);

        $filesystem = new Filesystem();
        $filesystem->copy(
            $config->getDirectory().DIRECTORY_SEPARATOR.$config->getJsonFile(),
            $tmpConfig->getDirectory().DIRECTORY_SEPARATOR.$tmpConfig->getJsonFile(),
            true
        );

        $event->setConfiguration($tmpConfig);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            BowerEvents::PRE_EXEC => array('onPreExec', 10),
        );
    }

}
