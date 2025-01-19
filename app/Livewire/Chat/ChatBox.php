<?php

namespace App\Livewire\Chat;

use App\Events\SendMessage;
use \App\Events\MessageRead;
//use \App\Notifications\MessageRead;
use App\Models\Message;
use App\Notifications\MessageSent;

use Livewire\Component;

class ChatBox extends Component
{


    public $selectedConversation ;
    public $body ;
    public $loadedMessages ;
    public  $pagainateVar = 10;


    protected $listeners = ['loadMore' , 'refresh'=>'$refresh' ];


    public function getListeners()
    {

        $auth_id = auth()->user()->id;

        return [

            //"echo-private:users.{$auth_id},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'broadcastedNotifications',
            "echo-private:users.{$auth_id},.App\\Events\\SendMessage" => 'broadcastedNotifications',
            'loadMore'
        ];
    }

    public function broadcastedNotifications($event)
    {

        //dd($event);


//        if ($event['type_e'] == MessageSent::class) {

            if ($event['conversation_id'] == $this->selectedConversation->id) {

                $this->dispatch('scroll-bottom');


                $newMessage = Message::find($event['message_id']);

                #push message in page of receiver (realtime)
                $this->loadedMessages->push($newMessage);

                #mark as read
                $newMessage->read_at = now();
                $newMessage->save();

                #broadcast
//                $this->selectedConversation->getReceiver()
//                    ->notify(new MessageRead($this->selectedConversation->id , $this->selectedConversation->getReceiver()->id));


                broadcast(new MessageRead(
                    $this->selectedConversation->id,
                    $this->selectedConversation->getReceiver()->id,
                ));



            }
//        }
    }


    //used in  @scroll in chat-box.blade
    public function loadMore()
    {
        //dd('detected');
         $this->pagainateVar += 10;

         $this->loadMessages();

         $this->dispatch('update-chat-height');

    }


    public function loadMessages()
    {

        $userId = auth()->id();
        //get count
        $count = Message::where('conversation_id' , $this->selectedConversation->id )

            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->whereNull('sender_deleted_at');

            })->orWhere(function ($query) use ($userId) {

                $query->where('receiver_id', $userId)
                    ->whereNull('receiver_deleted_at');
            })
            ->count();

        //skip and query
        $this->loadedMessages = Message::where('conversation_id' , $this->selectedConversation->id )
            ->where(function ($query) use ($userId) {

                $query->where('sender_id', $userId)
                    ->whereNull('sender_deleted_at');
            })->orWhere(function ($query) use ($userId) {

                $query->where('receiver_id', $userId)
                    ->whereNull('receiver_deleted_at');
            })
            ->skip($count - $this->pagainateVar )   //
            ->take($this->pagainateVar)
            ->get();

       return $this->loadedMessages;

        //dd($this->loadedMessages);

    }

    public  function sendMessage()
    {

        $this->validate(['body' => 'required|string']);

        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'body' => $this->body

        ]);
        //reset input
        $this->reset("body");
//        dd($createdMessage);

        // scroll to bottom
        $this->dispatch('scroll-bottom');


        // push message in same page of sender
        $this->loadedMessages->push($createdMessage);

        //Update conversation
        $this->selectedConversation->updated_at = now() ;
        $this->selectedConversation->save();

        #refresh chatlist
        $this->dispatch('refresh');    // emit not work in livewire 3

        //broadcast
//        $this->selectedConversation->getReceiver()
//            ->notify(new MessageSent(
//                Auth()->User(),
//                $createdMessage,
//                $this->selectedConversation,
//                $this->selectedConversation->getReceiver()->id
//            ));


        broadcast(new SendMessage(
                Auth()->User(),
                $createdMessage,
                $this->selectedConversation,
                $this->selectedConversation->getReceiver()->id,
        ));




    }







    public function mount()
    {
        $this->loadMessages();
    }
    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}
