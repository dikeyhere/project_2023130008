    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        public function up()
        {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('status', ['Pending', 'In Progress', 'Completed'])->default('Pending');
                $table->date('due_date')->nullable();
                $table->unsignedBigInteger('project_id')->nullable();  // Foreign ke projects
                $table->unsignedBigInteger('assigned_to')->nullable();  // User ID assigned
                $table->timestamps();

                $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
                $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            });
        }

        public function down()
        {
            Schema::dropIfExists('tasks');
        }
    };
