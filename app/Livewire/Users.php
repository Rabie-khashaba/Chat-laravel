<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Conversation;
use Livewire\Component;

class Users extends Component
{



//    public function message($userId)
//     {
//         $createdConversation= Conversation::updateOrCreate(
//             [
//                 'sender_id'=>auth()->id(),
//                 'receiver_id'=>$userId
//             ]);
//
//         return redirect()->route('chat',['query'=>$createdConversation->id]);
//
//     }




    public function message($userId)
    {

        //  $createdConversation =   Conversation::updateOrCreate(['sender_id' => auth()->id(), 'receiver_id' => $userId]);

        $authenticatedUserId = auth()->id();
        # Check if conversation already exists
        $existingConversation = Conversation::where(function ($query) use ($authenticatedUserId, $userId) {
            $query->where('sender_id', $authenticatedUserId)
                ->where('receiver_id', $userId);
        })
            ->orWhere(function ($query) use ($authenticatedUserId, $userId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $authenticatedUserId);
            })->first();


        //another way to check conversation
//        //checkConversation   in model cocnversation
//        public function scopeCheckConversation($query , $sender_email , $reciever_email){
//        return $query->where('sender_email',$sender_email)->where('receiver_email',$reciever_email)->
//        orwhere('sender_email',$reciever_email)->where('receiver_email',$sender_email);}
//
//
//        $checkConversation = Conversation::checkConversation($this->auth_email , $email)->get();


        if ($existingConversation) {
            # Conversation already exists, redirect to existing conversation
            return redirect()->route('chat', ['query' => $existingConversation->id]);
        }

        # Create new conversation
        $createdConversation = Conversation::create([
            'sender_id' => $authenticatedUserId,
            'receiver_id' => $userId,
        ]);

        return redirect()->route('chat', ['query' => $createdConversation->id]);

    }


    public function render()
    {
        return view('livewire.users',[
            'users'=>User::where('id', '!=', auth()->id())->get()
        ]);
    }
}
