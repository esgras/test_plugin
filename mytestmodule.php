<?php
class MyTestModule extends Module
{
    private $tableName = 'mymod_comment';

    function __construct()
    {
        $this->name = 'mytestmodule';
        $this->tab = 'front_office_features';
        $this->version = '0.1';
        $this->author = 'vayn_esgras';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('My Module for Learning');
        $this->description = $this->l('Here is some description');
    }
    
  

    public function install() {
        return parent::install() 
            && $this->registerHook('displayProductTabContent')
                                && $this->registerHook('home')
        ;
    }

    public function uninstall() {
        return parent::uninstall();
    }


    public function processProductTabContent($params) {
        $db = Db::getInstance();

        if (Tools::isSubmit('mymod_pc_submit_comment')) {
            $id = Tools::getValue('id_product');
            $grade = Tools::getValue('grade');
            $comment = Tools::getValue('comment');
            $insert = array(
                'id_product' => (int) $id,
                'grade' => (int) $grade,
                'comment' => pSQL($comment),
                'date_add' => date('Y-m-d H:i:s')
            );

            $db->insert($this->tableName, $insert);
            $this->context->smarty->assign('new_comment_posted', true);
        }
        
    }

    public function assignProductTabContent() {
        
        $enable_grades = Configuration::get('MYMOD_GRADES');
        $enable_comments = Configuration::get('MYMOD_COMMENTS');
       
        
        $id_product = Tools::getValue('id_product');
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . $this->tableName . ' WHERE id_product=' . (int) $id_product;
        $comments = Db::getInstance()->executeS($sql);


        $this->context->controller->addCSS($this->_path . 'views/css/mymodcomments.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/mymodcomments.js', 'all');

        $this->context->controller->addCSS($this->_path.'views/css/star-rating.css', 'all');
        $this->context->controller->addJS($this->_path.'views/js/star-rating.js');

        $this->context->smarty->assign(array(
            'enable_grades' => $enable_grades,
            'enable_comments' => $enable_comments,
            'comments' => $comments
        ));

    }

    public function processConfiguration()
    {
        
        if (Tools::isSubmit('mymod_pc_form')) {
            
           $enable_grades = Tools::getValue('enable_grades');
           $enable_comments = Tools::getValue('enable_comments');
           Configuration::updateValue('MYMOD_GRADES', $enable_grades);
           Configuration::updateValue('MYMOD_COMMENTS', $enable_comments);
           $this->context->smarty->assign('confirmation', 'ok');
        }
        
       $enable_grades = Configuration::get('MYMOD_GRADES');
       $enable_comments = Configuration::get('MYMOD_COMMENTS');

       $this->context->smarty->assign(array('enable_comments' => $enable_comments, 'enable_grades' => $enable_grades));
       
    }


     public function getContent()
     {
         $this->processConfiguration();
         return $this->display(__FILE__, 'getContent.tpl');
     }
    
     

     /*
     */
    public function hookDisplayProductTabContent($params) {
       $this->processProductTabContent();
       $this->assignProductTabContent();
       return $this->display(__FILE__, 'displayProductTabContent.tpl');
    }


    /**
     * @return string
     */
    public function testWorkWithHome()
    {
        $context = $this->context;
        $smarty = $context->smarty;
        $language = $context->language;
        $shop = $context->shop;
        $cart = $context->cart;
        $link = $context->link;
        $cookie = $context->cookie;
        $customer = $context->customer;
        $controller = $context->controller;
        $currency = $context->currency;
        #d($controller);
        #var_dump($context); die;
    }

    /**
     * @return string
     */
    public function addScripts()
    {
        #$this->context->controller->addCSS($this->_path . 'views/css/my.css', 'all');
        #$this->context->controller->addJS($this->_path . 'views/js/mytest.js');
    }

    public function hookHome($params) {
        $this->testWorkWithHome();
        $this->addScripts();

        return $this->display(__FILE__, 'home.tpl');
    }
    

}