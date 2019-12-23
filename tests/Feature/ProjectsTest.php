<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    //refreshdatabase ensures that the database is set back to previous state after test
    use WithFaker, RefreshDatabase;
    /**
     * @test
     
     */
    public function test_only_authenticated_users_can_create_projects()
    {
      //$this->withoutExceptionHandling();
      //sign a user in, otherwise "Unauthenticated" error 
      

      $attributes = factory('App\Project')->raw();

      //if the created project has no owner, then assert an error
      $this->post('/projects',$attributes)->assertRedirect('login');
    }

    public function test_a_user_can_create_a_project()
    {
        $this->withoutExceptionHandling();
      $this->actingAs(factory('App\User')->create());

     
        $attributes= [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph

       ];
       //when the user creates a new project with the attributes
       $this->post('/projects',$attributes)->assertRedirect('/projects');

       //test that the project attributes are inserted into the database
       $this->assertDatabaseHas('projects',$attributes);

       //test that when user goes to /projects, he sees the title of the project inserted 
       $this->get('/projects')->assertSee($attributes['title']);

    }

    public function test_a_user_can_view_a_project()
    {
      //to see the exact exception that is being thrown
      $this->withoutExceptionHandling();

      $project = factory('App\Project')->create();
      //when the user goes to the specific project page, he see the attributes of the project
      $this->get($project->path())
        ->assertSee($project->title)
        ->assertSee($project->description);
    }
    
    public function test_a_project_requires_a_title()
    {
      
      $this->actingAs(factory('App\User')->create());

      $attributes = factory('App\Project')->raw(['title'=>'']);

      //if the created project has no attributes, then assert an error
      $this->post('/projects',$attributes)->assertSessionHasErrors('title');
    }

    public function test_a_project_requires_a_description()
    {
      $this->actingAs(factory('App\User')->create());

      $attributes = factory('App\Project')->raw(['description'=>'']);

      //if the created project has no description, then assert an error
      $this->post('/projects',$attributes)->assertSessionHasErrors('description');
    }

    


    
}
