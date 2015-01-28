<?php
// src/Arii/AdminBundle/Controller/DBController.php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DBController extends Controller
{
    public function usersAction()
    {
        require_once '../vendor/dhtmlx/dhtmlxConnector/codebase/grid_connector.php';

        $database_host = $this->container->getParameter('database_host'); 
        $database_port = $this->container->getParameter('database_port'); 
        $database_name = $this->container->getParameter('database_name'); 
        $database_user = $this->container->getParameter('database_user'); 
        $database_password = $this->container->getParameter('database_password'); 

        $conn=mysql_connect( $database_host, $database_user,  $database_password );
        mysql_select_db( $database_name );

        $data = new \GridConnector($conn);
        $sql = "select ID,USERNAME,EMAIL,LOCKED,EXPIRED,'ACTIVE' as STATUS
        from arii_user
        order by username";
        $data->event->attach("beforeRender", array( $this, "color_rows") );
        $data->render_sql($sql,"ID","USERNAME,EMAIL,STATUS");
    }

    public function filtersAction()
    {
        require_once '../vendor/dhtmlx/dhtmlxConnector/codebase/grid_connector.php';

        $database_host = $this->container->getParameter('database_host'); 
        $database_port = $this->container->getParameter('database_port'); 
        $database_name = $this->container->getParameter('database_name'); 
        $database_user = $this->container->getParameter('database_user'); 
        $database_password = $this->container->getParameter('database_password'); 

        $conn=mysql_connect( $database_host, $database_user,  $database_password );
        mysql_select_db( $database_name );

        $data = new \GridConnector($conn);
        $data->render_table('arii_filter',"ID","FILTER,TITLE,SPOOLER,JOB,JOB_CHAIN,ORDER_ID,STATUS");
    }

    public function filter_formAction()
    {
        require_once '../vendor/dhtmlx/dhtmlxConnector/codebase/form_connector.php';

        $database_host = $this->container->getParameter('database_host'); 
        $database_port = $this->container->getParameter('database_port'); 
        $database_name = $this->container->getParameter('database_name'); 
        $database_user = $this->container->getParameter('database_user'); 
        $database_password = $this->container->getParameter('database_password'); 

        $conn=mysql_connect( $database_host, $database_user,  $database_password );
        mysql_select_db( $database_name );

        $data = new \FormConnector($conn);
        $data->render_table('arii_filter',"ID","filter,title,spooler,job,job_chain,order_id,status");
    }

    public function filter_gridAction()
    {
        require_once '../vendor/dhtmlx/dhtmlxConnector/codebase/grid_connector.php';

        $database_host = $this->container->getParameter('database_host'); 
        $database_port = $this->container->getParameter('database_port'); 
        $database_name = $this->container->getParameter('database_name'); 
        $database_user = $this->container->getParameter('database_user'); 
        $database_password = $this->container->getParameter('database_password'); 

        $conn=mysql_connect( $database_host, $database_user,  $database_password );
        mysql_select_db( $database_name );

        $data = new \GridConnector($conn);
        $data->render_table('arii_filter',"ID","filter,title,spooler,job,job_chain,order_id,status");
    }

}


