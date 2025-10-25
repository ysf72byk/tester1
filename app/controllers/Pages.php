<?php
  class Pages extends Controller {
    public function __construct(){

    }

    public function index(){
      $data = [
        'title' => 'Doktor Sepeti',
        'description' => 'Doktor Sepeti, doktorları ve hastaları bir araya getiren bir platformdur.'
      ];

      $this->view('pages/index', $data);
    }

    public function about(){
      $data = [
        'title' => 'Hakkımızda',
        'description' => 'Doktor Sepeti, hastaların en iyi doktorları bulmalarına ve randevu almalarına yardımcı olmak için tasarlanmıştır.'
      ];

      $this->view('pages/about', $data);
    }
  }
