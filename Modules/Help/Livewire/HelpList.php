<?php


namespace Modules\Help\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\CommonMarkConverter;

class HelpList extends Component
{
    public $files = [];
    public $search = '';
    public $currentFile = null;
    public $currentContent = '';
    public $collapsed = false;
    public $darkMode = true;

    public function mount($currentFile = null)
    {
        $this->loadFiles();

        // Mặc định mở file mới nhất
        if ($currentFile && $this->fileExists($currentFile)) {
            $this->openFile($currentFile);
        } elseif (count($this->files) > 0) {
            $this->openFile($this->files[0]['name']);
        }
    }
    public function toggleTheme()
    {
        $this->darkMode = !$this->darkMode;
    }

    public function loadFiles()
    {
        $helpFiles = Storage::disk('public')->files('help');

        $this->files = collect($helpFiles)
            ->filter(fn($file) => str_ends_with($file, '.md'))
            ->map(fn($file) => [
                'name' => pathinfo($file, PATHINFO_FILENAME),
                'modified' => Storage::disk('public')->lastModified($file),
            ])
            ->sortByDesc('modified')
            ->values()
            ->toArray();
    }

    public function updatedSearch()
    {
        $search = strtolower($this->search);
        $this->loadFiles();
        if ($search) {
            $this->files = collect($this->files)
                ->filter(fn($file) => str_contains(strtolower($file['name']), $search))
                ->toArray();
        }
    }

    public function fileExists($filename)
    {
        return Storage::disk('public')->exists("help/{$filename}.md");
    }

    public function openFile($filename)
    {
        $path = storage_path("app/public/help/{$filename}.md");

        if (!file_exists($path)) {
            $this->currentContent = '<div class="alert alert-danger">File không tồn tại.</div>';
            $this->currentFile = null;
            return;
        }

        $markdown = file_get_contents($path);
        $converter = new CommonMarkConverter();
        $this->currentContent = $converter->convert($markdown)->getContent();
        $this->currentFile = $filename;
    }

    public function toggleSidebar()
    {
        $this->collapsed = !$this->collapsed;
    }

    public function render()
    {
          return view('Help::livewire.help-list');
    }
}
