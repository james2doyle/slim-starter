<?php

namespace Application\Models;

use ParagonIE\EasyDB\Factory as EasyDB;
use Latitude\QueryBuilder\QueryFactory;
use Latitude\QueryBuilder\Conditions;
use Latitude\QueryBuilder\ValueList;

/**
 * The basic model
 */
class BaseModel
{
    /**
     * @var Array
     */
    private $results;

    /**
     * @var \ParagonIE\EasyDB\EasyDB
     */
    public $db;

    /**
     * @var Mixed (Latitude\QueryBuilder\...)
     */
    public $query;

    /**
     * @var String
     */
    protected $table = '';

    /**
     * @var String
     */
    protected $primary_key = 'id';

    /**
     * @var Array
     */
    protected $hidden = [];

    public function __construct()
    {
        if (!property_exists($this, 'table') || empty($this->table)) {
            throw new \Exception('Missing required table property', 500);
        }

        if (!property_exists($this, 'primary_key') || empty($this->primary_key)) {
            throw new \Exception('Missing required primary_key property', 500);
        }

        $settings = include(__DIR__ . '/../settings.php');
        $database = $settings['database'][getenv('APP_ENV')];

        $this->db = EasyDB::create(
            sprintf('%s:host=%s;dbname=%s', $database['adapter'], $database['host'], $database['name']),
            $database['user'],
            $database['pass'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            ]
        );

        $this->query = new QueryFactory($database['adapter']);
        $this->results = [];
    }

    public function all()
    {
        $this->query = $this->query->select();

        return $this->run();
    }

    public function findAll(array $ids)
    {
        return $this->select()
            ->where(Conditions::make($this->primary_key . ' IN ?', ValueList::make($ids)));
    }

    public function find(int $id) : array
    {
        return $this->findAll([$id])->limit(1);
    }

    public function run()
    {
        $sql = $this->query->from($this->table)->sql();
        // var_dump($sql, $this->query->params());
        $this->results = $this->db->run($sql, ...$this->query->params());
        return $this->filterResults($this->results);
    }

    public function raw()
    {
        $sql = $this->query->from($this->table)->sql();
        // var_dump($sql, $this->query->params());
        $this->results = $this->db->run($sql, ...$this->query->params());
        return $this->results;
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this, $name)) {
            $this->{$name}(...$arguments);
        } elseif (method_exists($this->query, $name)) {
            $this->query = $this->query->{$name}(...$arguments);
        }

        return $this;
    }

    private function filterResults(array $array)
    {
        return array_map(function (\stdClass $item) {
            // unset hidden fields
            if (!empty($this->hidden)) {
                foreach ($this->hidden as $key) {
                    unset($item->{$key});
                }
            }
            return $item;
        }, $array);
    }
}
