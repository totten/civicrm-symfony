<?php

/*
 *
 * (c) 2012 CiviCRM
 *
 */

namespace Civi\DrupalBundle;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;


/**
 * @author Tim Otten <to-git@think.hm>
 */
class DrupalSessionStorage extends NativeSessionStorage
{

    /**
     * Constructor.
     *
     * Depending on how you want the storage driver to behave you probably
     * want top override this constructor entirely.
     *
     * List of options for $options array with their defaults.
     * @see http://php.net/session.configuration for options
     * but we omit 'session.' from the beginning of the keys for convenience.
     *
     * ("auto_start", is not supported as it tells PHP to start a session before
     * PHP starts to execute user-land code. Setting during runtime has no effect).
     *
     * cache_limiter, "nocache" (use "0" to prevent headers from being sent entirely).
     * cookie_domain, ""
     * cookie_httponly, ""
     * cookie_lifetime, "0"
     * cookie_path, "/"
     * cookie_secure, ""
     * entropy_file, ""
     * entropy_length, "0"
     * gc_divisor, "100"
     * gc_maxlifetime, "1440"
     * gc_probability, "1"
     * hash_bits_per_character, "4"
     * hash_function, "0"
     * name, "PHPSESSID"
     * referer_check, ""
     * serialize_handler, "php"
     * use_cookies, "1"
     * use_only_cookies, "1"
     * use_trans_sid, "0"
     * upload_progress.enabled, "1"
     * upload_progress.cleanup, "1"
     * upload_progress.prefix, "upload_progress_"
     * upload_progress.name, "PHP_SESSION_UPLOAD_PROGRESS"
     * upload_progress.freq, "1%"
     * upload_progress.min-freq, "1"
     * url_rewriter.tags, "a=href,area=href,frame=src,form=,fieldset="
     *
     * @param array       $options Session configuration options.
     * @param object      $handler SessionHandlerInterface.
     * @param MetadataBag $metaBag MetadataBag.
     */
    public function __construct(array $options = array(), $handler = null, MetadataBag $metaBag = null)
    {
        ini_set('session.cache_limiter', ''); // disable by default because it's managed by HeaderBag (if used)
        ini_set('session.use_cookies', 1);

        /*
        // Drupal handles this
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            session_register_shutdown();
        } else {
            register_shutdown_function('session_write_close');
        }
        */

        $this->setMetadataBag($metaBag);
        $this->setOptions($options);
        $this->setSaveHandler($handler);
    }

    /**
     * Starts the session.
     *
     * @api
     */
    public function start()
    {
        if (!function_exists('drupal_bootstrap') || drupal_bootstrap() != DRUPAL_BOOTSTRAP_FULL) {
          throw new \Exception('Drupal must be pre-initialized!');
        }
    }

    /**
     * Regenerates id that represents this storage.
     *
     * @param  Boolean $destroy Destroy session when regenerating?
     *
     * @return Boolean True if session regenerated, false if error
     *
     * @throws \RuntimeException If an error occurs while regenerating this storage
     *
     * @api
     */
    public function regenerate($destroy = false, $lifetime = null)
    {
        if (null !== $lifetime) {
            ini_set('session.cookie_lifetime', $lifetime);
        }

        if ($destroy) {
            $this->metadataBag->stampNew();
        }

        drupal_session_regenerate();
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        drupal_session_commit();

        if (!$this->saveHandler->isWrapper() && !$this->getSaveHandler()->isSessionHandlerInterface()) {
            $this->saveHandler->setActive(false);
        }

        $this->closed = true;
    }
}
