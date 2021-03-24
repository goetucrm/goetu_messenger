<?php

use App\Models\ChatGroup;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupMessageStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_message_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('gc_id');
            $table->foreign('gc_id')->references('id')->on('chat_groups');
            $table->unsignedInteger('member_id');
            $table->foreign('member_id')->references('id')->on('users');
            $table->integer('unseen_messages');
            $table->timestamps();
        });

        $gc = ChatGroup::get();
        foreach ($gc as $key => $val) {
            $members = $val->members()->get();
            $messageCount = $val->messages()->count();
            foreach ($members as $member) {
                DB::table('group_message_statuses')->insert([
                    'gc_id' => $val->id,
                    'member_id' => $member->uid,
                    'unseen_messages' => $messageCount,
                    'created_at' => date('Y-m-d h:i:s')
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_message_statuses');
    }
}
