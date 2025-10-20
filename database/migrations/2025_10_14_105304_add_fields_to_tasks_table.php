   <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        public function up()
        {
            Schema::table('tasks', function (Blueprint $table) {
                $table->enum('priority', ['high', 'medium', 'low'])->default('low');
                $table->integer('progress')->default(0);
            });
        }

        public function down()
        {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['project_id']);
                $table->dropForeign(['assigned_to']);
                $table->dropColumn(['status', 'deadline', 'priority', 'progress', 'project_id', 'assigned_to']);
            });
        }
    };
