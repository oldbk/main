<?php
/**
 * Enabling total debug mode
 */

# In debug mode we really want to print errors to client
ini_set('display_errors', true);
# Report absolutely everything
error_reporting(-1);