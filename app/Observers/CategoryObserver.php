<?php

namespace App\Observers;

use App\Models\category;
use App\Models\Log;
use App\Traits\Loggable;

class CategoryObserver
{
    use Loggable;

    public function __construct()
    {
        $this->model = Category::class;
    }

    /**
     * Handle the category "created" event.
     */
    public function created(category $category): void
    {
        $this->log('create', $category->id, $category->toArray(), $this->model);
    }

    /**
     * Handle the category "updated" event.
     */
    public function updated(category $category): void
    {
        $this->updateLog($category, $this->model);
    }

    /**
     * Handle the category "deleted" event.
     */
    public function deleted(category $category): void
    {
        $this->log('delete', $category->id, $category->toArray(), $this->model);
    }

    /**
     * Handle the category "restored" event.
     */
    public function restored(category $category): void
    {
        $this->log('restore', $category->id, $category->toArray(), $this->model);
    }

    /**
     * Handle the category "force deleted" event.
     */
    public function forceDeleted(category $category): void
    {
        $this->log('force delete', $category->id, $category->toArray(), $this->model);
    }

}
