<?php

// Caminho: /public_html/modules/multi_pipeline/controllers/Kanban.php

class Kanban extends CI_Controller {

    public function index() {
        $this->load->model('MultiPipeline_model');

        $pipelines = $this->MultiPipeline_model->get_pipelines();
        $stages = $this->MultiPipeline_model->get_stages();
        $leads = $this->MultiPipeline_model->get_leads();

        $data = array(
            'pipelines' => $pipelines,
            'stages' => $stages,
            'leads' => $leads
        );

        $this->load->view('kanban', $data);
    }
}