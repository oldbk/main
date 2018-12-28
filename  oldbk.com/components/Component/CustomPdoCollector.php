<?php


namespace components\Component;


use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use Illuminate\Database\DatabaseManager;

class CustomPdoCollector extends PDOCollector
{

    /**
     * @var DatabaseManager $capsule
     */
    private $traceablePDO;

    private $name;

    public function __construct(TraceablePDO $traceablePDO, $name = 'default')
    {
        $this->traceablePDO = $traceablePDO;
        $this->name = $name;

        parent::__construct();
        $this->addConnection($this->traceablePDO, $name);
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return array(
            "database" => array(
                "icon" => "database",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => $this->name,
                "default" => "[]"
            ),
            "database:badge" => array(
                "map" => $this->name . ".nb_statements",
                "default" => 0
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}