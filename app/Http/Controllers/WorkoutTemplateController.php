<?php
namespace App\Http\Controllers;
use App\Models\Workout;
use App\Models\WorkoutTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkoutTemplateController extends Controller
{
    public function storeFromWorkout(Request $request, Workout $workout)
    {
        $data=$request->validate(['name'=>['required','string','max:120'],'description'=>['nullable','string','max:1000']]);
        DB::transaction(function()use($data,$request,$workout){$template=WorkoutTemplate::create([...$data,'created_by'=>$request->user()->id,'active'=>true]);$template->items()->createMany($workout->items->map(fn($item)=>['exercise_id'=>$item->exercise_id,'position'=>$item->position,'sets'=>$item->sets,'repetitions'=>$item->repetitions,'rest_seconds'=>$item->rest_seconds])->all());});
        return back()->with('success','Modelo criado. Agora ele pode ser atribuído a outros alunos.');
    }
    public function destroy(WorkoutTemplate $workoutTemplate){$workoutTemplate->update(['active'=>false]);return back()->with('success','Modelo arquivado.');}
}
