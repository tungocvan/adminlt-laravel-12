<?php

namespace App\Livewire\Env;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class EnvList extends Component
{
    public $envVariables = [];
    public $selectedKey;
    public $selectedValue;
    public $showModal = false;
    public $errorMessage = '';
    public $newKey;
    public $newValue;

    public function mount()
    {
        $this->loadEnvVariables();
    }

    public function loadEnvVariables()
    {
        $envPath = base_path('.env');
        $this->envVariables = array_filter(file($envPath), 'trim');
    }

    public function openModal($key, $value)
    {
        $this->selectedKey = $key;
        $this->selectedValue = $value;
        $this->showModal = true;
        $this->errorMessage = ''; // Reset error message
    }

    public function updateEnv()
    {
        // Chuyển đổi key thành chữ hoa
        $this->selectedKey = strtoupper($this->selectedKey);

        if ($this->selectedKey === 'APP_NAME' && strpos($this->selectedValue, ' ') !== false) {
            $this->errorMessage = 'Giá trị của APP_NAME không được chứa khoảng trắng.';
            return;
        }

        $this->envVariables = array_map(function($line) {
            return strpos($line, $this->selectedKey) === 0 
                ? "{$this->selectedKey}={$this->selectedValue}" 
                : $line;
        }, $this->envVariables);
        
        $this->writeEnv();
        $this->resetModal();
    }

    public function deleteEnv($key)
    {
        $this->envVariables = array_filter($this->envVariables, function($line) use ($key) {
            return strpos($line, $key) === false;
        });
        $this->writeEnv();
    }

    public function addItem()
    {
        // Kiểm tra nếu key và value không được bỏ trống
        if (empty(trim($this->newKey))) {
            $this->errorMessage = 'Key không được để trống.';
            return;
        }

        if (empty(trim($this->newValue))) {
            $this->errorMessage = 'Value không được để trống.';
            return;
        }

        // Kiểm tra nếu key có khoảng trắng hoặc ký tự đặc biệt
        if (preg_match('/[^A-Za-z0-9_]/', $this->newKey)) {
            $this->errorMessage = 'Key chỉ được chứa chữ cái, số và dấu gạch dưới.';
            return;
        }

        // Chuyển đổi key thành chữ hoa
        $this->newKey = strtoupper($this->newKey);

        // Kiểm tra nếu có khoảng trắng trong value
        if (strpos($this->newValue, ' ') !== false) {
            $this->newValue = '"' . $this->newValue . '"'; // Thêm nháy đôi vào value
        }

        // Thêm cặp mới vào mảng
        $this->envVariables[] = "{$this->newKey}={$this->newValue}";
        $this->writeEnv();

        // Reset các trường nhập
        $this->newKey = '';
        $this->newValue = '';
        $this->errorMessage = ''; // Reset error message
    }

    public function writeEnv()
    {
        $envPath = base_path('.env');
        $newEnvContent = implode(PHP_EOL, $this->envVariables) . PHP_EOL;
        File::put($envPath, $newEnvContent);
        $this->loadEnvVariables();
    }

    public function resetModal()
    {
        $this->selectedKey = null;
        $this->selectedValue = null;
        $this->showModal = false;
        $this->errorMessage = ''; // Reset error message
    }

    public function render()
    {
        return view('livewire.env.env-list');
    }
}