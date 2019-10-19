<?php

namespace App\Listeners;

use App\Events\CategoryCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TaxonomyUpdateNextCode
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CategoryCreated  $event
     * @return void
     */
    public function handle(CategoryCreated $event)
    {
        $taxonomy = $event->cat->taxonomy;
        if($taxonomy->autogen && $taxonomy->in_pc)
        {
            $cc = $taxonomy->next_code;
            $ct = $taxonomy->code_type;
            $cl = $taxonomy->code_length;
            $ct_arr = explode('-',$ct);
            $cc_arr = str_split($cc);
            $jump_next = 1;
            for($i=$cl-1 ; $i>=0 ; $i--){
                if($ct_arr[$i] == 'alpha'){
                    if($jump_next){
                        $curr = $cc_arr[$i];
                        $next = chr(((ord($cc_arr[$i])-65+1)%26)+65);
                        $cc_arr[$i] = $next;
                    }
                    if($curr== 'Z' && $next == 'A'){
                        $jump_next = 1;
                    }
                    else{
                        $jump_next = 0;
                    }
                }
                else{
                    if($jump_next){
                        $curr = $cc_arr[$i];
                        $next = chr(((ord($cc_arr[$i])-48+1)%10)+48);
                        $cc_arr[$i] = $next;
                    }
                    if($curr== '9' && $next == '0'){
                        $jump_next = 1;
                    }
                    else{
                        $jump_next = 0;
                    }
                }
            }
            $cc = implode("",$cc_arr);
            $taxonomy->next_code = $cc;
            $taxonomy->save();
        }
    }
}
