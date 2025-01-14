<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;

class Chat extends Component
{


    public $query;   // id of $createdConversation or $existingConversation
    public $selectedConversation;


    public function mount()
    {
        $this->selectedConversation = Conversation::find($this->query);

        Message::where('conversation_id', $this->selectedConversation->id)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);



//        dd($this->selectedConversation);
    }
    public function render()
    {
        return view('livewire.chat.chat');
    }
}
