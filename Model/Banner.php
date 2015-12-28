<?php
/**
 ************************************************************************
 * @copyright 2015 David Lima
 * @license Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0) 
 ************************************************************************
 */
namespace Fennec\Modules\Slider\Model;

use \Fennec\Model\Base;
use \Fennec\Library\PHPImageWorkshop\ImageWorkshop;

/**
 * Banner
 *
 * @author David Lima
 * @version r1.0
 */
class Banner extends Base
{
    use \Fennec\Library\Urls;

    /**
     * Path to send uploads (must have write permissions)
     *
     * @var string
     */
    const UPLOAD_DIR = parent::UPLOAD_BASE_DIR . 'slider';

    /**
     * Table to save data
     *
     * @var string
     */
    public static $table = "banners";

    /**
     * Banner title
     * @var string(128)
     */
    public $title;

    /**
     * Banner URL
     * @var string(255)
     */
    public $url;
    
    /**
     * Datetime to start showing banner
     * @var string|datetime
     */
    public $startdate;
    
    /**
     * Datetime to stop showing banner
     * @var string|datetime
     */
    public $enddate;
    
    /**
     * Banner ordering position
     * @var int
     */
    public $position;
    
    /**
     * Banner image filename
     * @var string
     */
    public $image;
    
    /**
     * Banner ID
     * @var int
     */
    public $id;
    

    /**
     * Creates or update a new banner
     *
     * @return PDOStatement
     */
    public function save()
    {
        $data = $this->prepare();

        if (isset($data['valid']) && ! $data['valid']){
            return $data;
        } else {
            try {
                if ($this->id) {
                    $banner = $this->getByColumn('id', $this->id)[0];
                    $this->image = $banner->image;
                    $query = $this->update(self::$table)
                        ->set($data)
                        ->where("id = '{$this->id}'")
                        ->execute();
                } else {
                    $query = $this->insert($data)
                        ->into(self::$table)
                        ->execute();
                    
                    $this->id = $query;
                }

                return array(
                    'result' => (isset($banner) ? 'Banner updated!' : 'Banner created!')
                );
            } catch (\Exception $e) {
                return array(
                    'result' => 'Failed to ' . (isset($banner) ? 'update' : 'create') . ' banner!',
                    'errors' => array($e->getMessage())
                );
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Fennec\Model\Base::getAll()
     */
    public function getAll()
    {
        return $this->select("*")
            ->from(self::$table)
            ->order('position', 'ASC')
            ->execute();
    }
    
    /**
     * Return all active banners based on date
     * 
     * @return PDOStatement
     */
    public function getAllActive()
    {
        return $this->select("*")
            ->from(self::$table)
            ->where('startdate <= NOW() AND (enddate > NOW() OR enddate IS NULL)')
            ->order('position', 'ASC')
            ->execute();
    }
    
    /**
     * Runs a SQL DELETE statement
     * 
     * @param int $id Banner ID to remove
     * @return PDOStatement
     */
    public function remove($id)
    {
        $id = (int) $id;
        return $this->delete()
            ->from(self::$table)
            ->where("id = $id")
            ->execute();
    }
    
    /**
     * Run several SQL UPDATE statements to reorganize all banners
     * @param array $banners
     * @return boolean
     */
    public function reorder(array $banners)
    {
        foreach ($banners as $banner) {
            $id = (int) $banner['id'];
            $data = array(
                'position' => (int) $banner['index']
            );
            $this->update(self::$table)
                ->set($data)
                ->where("id = $id")
                ->execute();
        }
        
        return true;
    }

    /**
     * Prepare data to create/update banner
     *
     * @return multitype:string |multitype:\Fennec\Model\string \Fennec\Model\integer
     */
    private function prepare()
    {
        $errors = $this->validate();
        if (! $errors['valid']) {
            return $errors;
        }

        $this->title = filter_var($this->title, \FILTER_SANITIZE_STRING);
        $this->startdate = filter_var($this->startdate, \FILTER_SANITIZE_STRING);
        $this->enddate = ($this->enddate? filter_var($this->enddate, \FILTER_SANITIZE_STRING) : null);
        $this->url = filter_var($this->url, \FILTER_SANITIZE_STRING);

        if (isset($_FILES['image']) && ! empty($_FILES['image']['name'])) {
            $this->image = uniqid('banner') . '.png';
            $image = ImageWorkshop::initFromPath($_FILES['image']['tmp_name']);
            $image->save(self::UPLOAD_DIR, $this->image, true);
        } elseif ($this->id) {
            $banner = $this->getByColumn('id', $this->id)[0];
            $this->image = $banner->image;
        }
        
        $result = array(
            'title' => $this->title,
            'startdate' => $this->startdate,
            'url' => $this->url,
            'image' => $this->image
        );
        
        if ($this->enddate) {
            $result['enddate'] = $this->enddate;
        } elseif ($this->id && ! $this->enddate) {
            $result['enddate'] = null;
        }
        
        return $result;
    }

    /**
     * Validate post data
     *
     * @return multitype:string
     */
    private function validate()
    {
        $validation = array(
            'valid' => true,
            'errors' => array()
        );

        if (! $this->title) {
            $validation['valid'] = false;
            $validation['errors']['title'] = "Title is a required field";
        }
        
        if (! $this->id && (! isset($_FILES['image']) || isset($_FILES['image']) && empty($_FILES['image']['name']))) {
            $validation['valid'] = false;
            $validation['errors']['image'] = "Image is a required field";
        }

        return $validation;
    }
}
