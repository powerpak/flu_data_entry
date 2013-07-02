<?php
require 'session.php';
/**
 * Session database driver.
 *
 * $Id: Database.php 4134 2009-03-28 04:37:54Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Session_Database_Driver implements Session_Driver {

  /*
  CREATE TABLE session
  (
    session_id VARCHAR(127) NOT NULL,
    last_activity INT(10) UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    PRIMARY KEY (session_id)
  );
  */

  // Database settings
  protected $table = 'Session';

  // Session settings
  protected $session_id;
  protected $written = FALSE;

  public function __construct($database_config)
  {
    $this->model = $database_config['session_model'];
  }

  public function open($path, $name)
  {
    return TRUE;
  }

  public function close()
  {
    return TRUE;
  }

  public function read($id)
  {
    // Load the session
    $session = Model::factory($this->model)->where('session_id', $id)->find_one();

    if (!$session->loaded()) {
      // No current session
      $this->session_id = NULL;

      return '';
    }

    // Set the current session id
    $this->session_id = $id;

    // Load the data
    $data = $session->data;

    return base64_decode($data);
  }

  public function write($id, $data)
  {
    $session = Model::factory($this->model);

    if ($this->session_id === NULL)
    {
      // Insert a new session
      $session->session_id = $id;
      $session->last_activity = time();
      $session->data = base64_encode($data);
      $session->save();
    }
    elseif ($id === $this->session_id)
    {
      // Do not update the session_id
      // Update the existing session
      $session = $session->where('session_id', $id)->find_one();
      if ($session->loaded())
      {
        $session->last_activity = time();
        $session->data = base64_encode($data);
        $session->save();
      }
    }
    else
    {
      // Update the session and id
      $session = $session->where('session_id', $this->session_id)->find_one();
      if ($session->loaded())
      {
        $session->last_activity = time();
        $session->data = base64_encode($data);
        $session->session_id = $id;
        $session->save();
      }

      // Set the new session id
      $this->session_id = $id;
    }

    return TRUE;
  }

  public function destroy($id)
  {
    // Delete the requested session
    $session = Model::factory($this->model)->where('session_id', $this->session_id)->find_one();
    if ($this->db->loaded()) { $this->db->delete(); }

    // Session id is no longer valid
    $this->session_id = NULL;

    return TRUE;
  }

  public function regenerate()
  {
    // Generate a new session id
    session_regenerate_id();

    // Return new session id
    return session_id();
  }

  public function gc($maxlifetime)
  {
    // Delete all expired sessions
    $expired = Model::factory($this->model)->where_lt('last_activity', time() - $maxlifetime)->find_many();
    foreach ($expired as $row)
    {
      $row->delete();
    }

    return TRUE;
  }

} // End Session Database Driver
