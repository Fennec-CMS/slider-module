<?php
/**
 ************************************************************************
 * @copyright 2015 David Lima
 * @license Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 ************************************************************************
 */
namespace Fennec\Modules\Slider\Controller\Admin;

use \Fennec\Controller\Admin\Index as AdminController;
use \Fennec\Modules\Slider\Model\Banner as BannerModel;

/**
 * Basic banner sliding module
 *
 * @author David Lima
 * @version r1.0
 */
class Index extends AdminController
{
    
    /**
     * Banner Model
     * @var \Fennec\Modules\Slider\Model\Banner
     */
    private $model;

    /**
     * Initial setup
     */
    public function __construct()
    {
        parent::__construct();
    
        $this->model = new BannerModel();
    
        $this->moduleInfo = array(
            'title' => 'Slider'
        );
    }
    
    /**
     * Default action
     */
    public function indexAction()
    {
        $this->list = $this->model->getAll();
    }
    
    /**
     * Manage banner creating/updating
     */
    public function formAction()
    {
        if ($this->getParam('id')) {
            $id = $this->model->id = (int) $this->getParam('id');
            $post = $this->model->getByColumn('id', $id);
            if (count($post)) {
                $this->post = $post[0];
                foreach($this->post as $param => $value){
                    $this->$param = $value;
                }
            } else {
                $link = $this->linkToRoute('admin-slider');
                header("Location: $link ");
            }
        }
        
        if ($this->isPost()) {
            try {
                foreach ($this->getPost() as $postKey => $postValue) {
                    $this->$postKey = $postValue;
                }
        
                $this->model->setTitle($this->getPost('title'));
                $this->model->setUrl($this->getPost('url'));
                $this->model->setStartDate($this->getPost('startdate'));
                $this->model->setEndDate($this->getPost('enddate'));
                
                $this->result = $this->model->create();
                if (isset($this->result['errors'])) {
                    $this->result['result'] = implode('<br>', $this->result['errors']);
                }
            } catch (\Exception $e) {
                $this->exception = $e;
                $this->throwHttpError(500);
            }
        }
    }
    
    /**
     * Try to delete a banner
     */
    public function deleteAction()
    {
        header("Content-Type: Application/JSON");
        
        $result = array(
            'errors' => true,
            'result' => $this->translate('Invalid request')
        );
        
        if ($this->getParam('id') && is_numeric($this->getParam('id'))) {
            try {
                $id = (int) $this->getParam('id');
                $this->model->remove($id);
                $result['errors'] = false;
                $result['result'] = $this->translate('Banner removed');
            } catch (\Exception $e) {
                $result['result'] = $e->getMessage();
            }
        }
        
        print_r(json_encode($result));
    }
    
    /**
     * Reorganize banners using column (position)
     */
    public function reorderAction()
    {
        header("Content-Type: Application/JSON");
        $result = array(
            'success' => false,
            'message' => 'Invalid request'
        );
        
        if ($this->isPost()) {
            if ($this->getPost('banners')) {
                $banners = $this->getPost('banners');
                if ($this->model->reorder($banners)) {
                    $result['success'] = true;
                    $result['message'] = 'OK';
                }
            }
        }
        
        print_r(json_encode($result));
    }
}
