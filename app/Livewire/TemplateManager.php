<?php

namespace App\Livewire;

use App\Models\DocumentTemplate;
use App\Models\Kriteria;
use App\Models\TemplateVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class TemplateManager extends Component
{
    use WithFileUploads, WithPagination;

    public $search = '';
    public $filterStatus = 'all';
    public $filterKriteria = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Template creation/editing
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingTemplate = null;

    public $templateName = '';
    public $templateDescription = '';
    public $templateKriteriaId = '';
    public $templateContent = '';
    public $templateVariables = [];
    public $templateFile;
    public $templatePreview;

    // Template usage
    public $showUseModal = false;
    public $usingTemplate = null;
    public $fillData = [];
    public $previewContent = '';

    protected $rules = [
        'templateName' => 'required|string|max:255',
        'templateDescription' => 'nullable|string|max:1000',
        'templateKriteriaId' => 'required|exists:kriterias,id',
        'templateContent' => 'required|string',
        'templateFile' => 'nullable|file|mimes:doc,docx,pdf|max:10240',
        'templatePreview' => 'nullable|image|max:2048'
    ];

    protected $listeners = [
        'refreshTemplates' => '$refresh'
    ];

    public function mount()
    {
        $this->resetTemplateForm();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterKriteria()
    {
        $this->resetPage();
    }

    public function createTemplate()
    {
        $this->validate();

        // Extract variables from content
        $variables = $this->extractVariablesFromContent($this->templateContent);

        $templateData = [
            'name' => $this->templateName,
            'description' => $this->templateDescription,
            'kriteria_id' => $this->templateKriteriaId,
            'created_by' => Auth::id(),
            'variables' => $variables
        ];

        // Handle file uploads
        if ($this->templateFile) {
            $filePath = $this->templateFile->store('templates', 'public');
            $templateData['file_path'] = $filePath;
        }

        if ($this->templatePreview) {
            $previewPath = $this->templatePreview->store('template-previews', 'public');
            $templateData['preview_path'] = $previewPath;
        }

        $template = DocumentTemplate::create($templateData);

        // Create initial version
        $template->createVersion([
            'content' => $this->templateContent,
            'variables' => $variables,
            'change_log' => 'Versi awal template'
        ]);

        $this->resetTemplateForm();
        $this->showCreateModal = false;

        session()->flash('message', 'Template berhasil dibuat.');
        $this->dispatch('refreshTemplates');
    }

    public function editTemplate($templateId)
    {
        $this->editingTemplate = DocumentTemplate::findOrFail($templateId);

        $this->templateName = $this->editingTemplate->name;
        $this->templateDescription = $this->editingTemplate->description;
        $this->templateKriteriaId = $this->editingTemplate->kriteria_id;
        $this->templateContent = $this->editingTemplate->currentVersion?->content ?? '';
        $this->templateVariables = $this->editingTemplate->variables ?? [];

        $this->showEditModal = true;
    }

    public function updateTemplate()
    {
        $this->validate();

        $variables = $this->extractVariablesFromContent($this->templateContent);

        $updateData = [
            'name' => $this->templateName,
            'description' => $this->templateDescription,
            'kriteria_id' => $this->templateKriteriaId,
            'variables' => $variables
        ];

        // Handle file uploads
        if ($this->templateFile) {
            // Delete old file if exists
            if ($this->editingTemplate->file_path) {
                Storage::disk('public')->delete($this->editingTemplate->file_path);
            }
            $filePath = $this->templateFile->store('templates', 'public');
            $updateData['file_path'] = $filePath;
        }

        if ($this->templatePreview) {
            // Delete old preview if exists
            if ($this->editingTemplate->preview_path) {
                Storage::disk('public')->delete($this->editingTemplate->preview_path);
            }
            $previewPath = $this->templatePreview->store('template-previews', 'public');
            $updateData['preview_path'] = $previewPath;
        }

        $this->editingTemplate->update($updateData);

        // Create new version if content changed
        $currentContent = $this->editingTemplate->currentVersion?->content ?? '';
        if ($currentContent !== $this->templateContent) {
            $this->editingTemplate->createVersion([
                'content' => $this->templateContent,
                'variables' => $variables,
                'change_log' => 'Update template content'
            ]);
        }

        $this->resetTemplateForm();
        $this->showEditModal = false;
        $this->editingTemplate = null;

        session()->flash('message', 'Template berhasil diperbarui.');
        $this->dispatch('refreshTemplates');
    }

    public function deleteTemplate($templateId)
    {
        $template = DocumentTemplate::findOrFail($templateId);

        // Check permissions (only creator or admin can delete)
        if ($template->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus template ini.');
            return;
        }

        // Delete associated files
        if ($template->file_path) {
            Storage::disk('public')->delete($template->file_path);
        }
        if ($template->preview_path) {
            Storage::disk('public')->delete($template->preview_path);
        }

        $template->delete();

        session()->flash('message', 'Template berhasil dihapus.');
        $this->dispatch('refreshTemplates');
    }

    public function publishTemplate($templateId)
    {
        $template = DocumentTemplate::findOrFail($templateId);

        if ($template->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mempublish template ini.');
            return;
        }

        $template->publish();
        session()->flash('message', 'Template berhasil dipublish.');
        $this->dispatch('refreshTemplates');
    }

    public function archiveTemplate($templateId)
    {
        $template = DocumentTemplate::findOrFail($templateId);

        if ($template->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengarsipkan template ini.');
            return;
        }

        $template->archive();
        session()->flash('message', 'Template berhasil diarsipkan.');
        $this->dispatch('refreshTemplates');
    }

    public function useTemplate($templateId)
    {
        $this->usingTemplate = DocumentTemplate::findOrFail($templateId);
        $this->fillData = [];
        $this->previewContent = '';

        // Initialize fill data with defaults
        foreach ($this->usingTemplate->getVariables() as $key => $variable) {
            $this->fillData[$key] = $variable['default'] ?? '';
        }

        $this->showUseModal = true;
    }

    public function updatedFillData()
    {
        if ($this->usingTemplate) {
            $this->previewContent = $this->usingTemplate->fillTemplate($this->fillData);
        }
    }

    public function createDocumentFromTemplate()
    {
        if (!$this->usingTemplate) {
            return;
        }

        // Validate required fields
        $variables = $this->usingTemplate->getVariables();
        $errors = [];

        foreach ($variables as $key => $variable) {
            if (($variable['required'] ?? true) && empty($this->fillData[$key])) {
                $errors["fillData.{$key}"] = "Field {$variable['label']} wajib diisi.";
            }
        }

        if (!empty($errors)) {
            $this->addError('template_validation', 'Mohon lengkapi semua field yang wajib diisi.');
            return;
        }

        // Create document from template
        $filledContent = $this->usingTemplate->fillTemplate($this->fillData);

        $document = \App\Models\Dokumen::create([
            'user_id' => Auth::id(),
            'kriteria_id' => $this->usingTemplate->kriteria_id,
            'prodi_id' => session('selected_prodi_id'),
            'template_id' => $this->usingTemplate->id,
            'nama_file' => $this->usingTemplate->name . ' - ' . now()->format('Y-m-d'),
            'file_path' => 'generated_from_template', // This would be replaced with actual file generation
            'versi' => '1.0',
            'status' => 'draft',
            'template_data' => $this->fillData,
            'metadata' => [
                'generated_from_template' => true,
                'template_version' => $this->usingTemplate->currentVersion?->version_number,
                'generated_content' => $filledContent
            ]
        ]);

        // Record template usage
        $this->usingTemplate->recordUsage();

        $this->showUseModal = false;
        $this->usingTemplate = null;
        $this->fillData = [];

        session()->flash('message', 'Dokumen berhasil dibuat dari template.');
        return redirect()->route('data-dukung'); // Redirect to document management
    }

    protected function extractVariablesFromContent($content)
    {
        preg_match_all('/\{\{(\w+)\}\}/', $content, $matches);

        $variables = [];
        if (!empty($matches[1])) {
            foreach (array_unique($matches[1]) as $variable) {
                $variables[$variable] = [
                    'name' => $variable,
                    'label' => ucwords(str_replace('_', ' ', $variable)),
                    'type' => 'text',
                    'required' => true,
                    'default' => ''
                ];
            }
        }

        return $variables;
    }

    protected function resetTemplateForm()
    {
        $this->templateName = '';
        $this->templateDescription = '';
        $this->templateKriteriaId = '';
        $this->templateContent = '';
        $this->templateVariables = [];
        $this->templateFile = null;
        $this->templatePreview = null;
    }

    public function getTemplates()
    {
        $query = DocumentTemplate::with(['kriteria', 'creator', 'currentVersion'])
            ->when($this->search, function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus !== 'all', function($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterKriteria !== 'all', function($q) {
                $q->where('kriteria_id', $this->filterKriteria);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    public function getKriteriaOptions()
    {
        return Kriteria::orderBy('kode')->get();
    }

    public function render()
    {
        return view('livewire.template-manager', [
            'templates' => $this->getTemplates(),
            'kriteriaOptions' => $this->getKriteriaOptions()
        ])->layout('layouts.app');
    }
}
