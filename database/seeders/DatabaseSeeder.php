<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Event;
use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuarios de prueba
        $pepe = User::factory()->create([
            'name' => 'Pepe - El Mejor Alumno',
            'email' => 'pepe@dba.com',
            'password' => bcrypt('password'),
        ]);

        $testUser = User::factory()->create([
            'name' => 'Usuario de Prueba',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Crear 3 usuarios adicionales
        $otherUsers = User::factory(3)->create();

        // Para Pepe, crear datos completos para demostración
        $this->seedPepeData($pepe);

        // Para el usuario de prueba, crear algunos datos
        $this->seedTestUserData($testUser);

        // Para otros usuarios, crear datos mínimos
        foreach ($otherUsers as $user) {
            $this->seedMinimalData($user);
        }
    }

    private function seedPepeData(User $user): void
    {
        // Proyecto 1: Sistema de Gestión Académica
        $project1 = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Sistema de Gestión Académica',
            'color' => '#3b82f6', // Azul
        ]);

        // Tareas del Proyecto 1
        $task1_1 = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project1->id,
            'title' => 'Diseñar base de datos del sistema',
            'description' => 'Crear diagrama ER y normalizar tablas para el sistema académico',
            'status' => TaskStatus::Completado,
            'priority' => TaskPriority::Alta,
            'due_date' => now()->subDays(10),
            'completed_at' => now()->subDays(11),
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_1->id,
            'title' => 'Identificar entidades principales',
            'is_completed' => true,
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_1->id,
            'title' => 'Definir relaciones entre entidades',
            'is_completed' => true,
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_1->id,
            'title' => 'Normalizar hasta 3FN',
            'is_completed' => true,
        ]);

        $task1_2 = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project1->id,
            'title' => 'Implementar módulo de autenticación',
            'description' => 'Login con Laravel Fortify y encriptación de contraseñas',
            'status' => TaskStatus::Completado,
            'priority' => TaskPriority::Alta,
            'due_date' => now()->subDays(5),
            'completed_at' => now()->subDays(6),
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_2->id,
            'title' => 'Configurar Fortify',
            'is_completed' => true,
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_2->id,
            'title' => 'Crear vistas de login',
            'is_completed' => true,
        ]);

        $task1_3 = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project1->id,
            'title' => 'Crear dashboard con drill down',
            'description' => 'Dashboard interactivo con 3 niveles de profundidad',
            'status' => TaskStatus::EnProgreso,
            'priority' => TaskPriority::Alta,
            'due_date' => now()->addDays(2),
            'completed_at' => null,
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_3->id,
            'title' => 'Nivel 1: Vista de proyectos',
            'is_completed' => true,
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_3->id,
            'title' => 'Nivel 2: Vista de tareas',
            'is_completed' => true,
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_3->id,
            'title' => 'Nivel 3: Vista de subtareas',
            'is_completed' => false,
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_3->id,
            'title' => 'Agregar navegación drill up',
            'is_completed' => false,
        ]);

        $task1_4 = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project1->id,
            'title' => 'Implementar indicadores visuales',
            'description' => 'Semáforo de rendimiento con leyenda y 3 estados',
            'status' => TaskStatus::Pendiente,
            'priority' => TaskPriority::Media,
            'due_date' => now()->addDays(5),
            'completed_at' => null,
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_4->id,
            'title' => 'Definir lógica del semáforo',
            'is_completed' => false,
        ]);

        Subtask::factory()->create([
            'task_id' => $task1_4->id,
            'title' => 'Crear vista con leyenda',
            'is_completed' => false,
        ]);

        // Proyecto 2: Dashboard de Ventas
        $project2 = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Dashboard de Ventas',
            'color' => '#10b981', // Verde
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project2->id,
            'title' => 'Análisis de requerimientos',
            'description' => 'Reunión con stakeholders para definir KPIs',
            'status' => TaskStatus::Completado,
            'priority' => TaskPriority::Alta,
            'due_date' => now()->subDays(20),
            'completed_at' => now()->subDays(21),
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project2->id,
            'title' => 'Gráficos de torta por categoría',
            'description' => 'Mostrar distribución de ventas por categoría de producto',
            'status' => TaskStatus::EnProgreso,
            'priority' => TaskPriority::Media,
            'due_date' => now()->addDays(7),
            'completed_at' => null,
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project2->id,
            'title' => 'Exportar reportes a PDF',
            'description' => 'Funcionalidad para generar reportes en PDF',
            'status' => TaskStatus::Pendiente,
            'priority' => TaskPriority::Baja,
            'due_date' => now()->addDays(15),
            'completed_at' => null,
        ]);

        // Proyecto 3: App Mobile
        $project3 = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Aplicación Móvil',
            'color' => '#8b5cf6', // Púrpura
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project3->id,
            'title' => 'Configurar React Native',
            'description' => 'Setup inicial del proyecto mobile',
            'status' => TaskStatus::Cancelada,
            'priority' => TaskPriority::Media,
            'due_date' => now()->subDays(3),
            'completed_at' => null,
        ]);

        // Tareas sin proyecto
        Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => null,
            'title' => 'Estudiar para examen de DBA',
            'description' => 'Repasar consultas SQL, drill down y semáforos',
            'status' => TaskStatus::EnProgreso,
            'priority' => TaskPriority::Alta,
            'due_date' => now()->addDays(1),
            'completed_at' => null,
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => null,
            'title' => 'Preparar presentación del proyecto',
            'description' => 'Slides explicando la implementación del dashboard',
            'status' => TaskStatus::Pendiente,
            'priority' => TaskPriority::Alta,
            'due_date' => now()->addDays(3),
            'completed_at' => null,
        ]);

        // Eventos para Pepe
        Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'Reunión con el profesor',
            'start_time' => now()->addDays(1)->setTime(14, 0),
            'end_time' => now()->addDays(1)->setTime(15, 0),
            'is_all_day' => false,
        ]);

        Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'Entrega del proyecto DBA',
            'start_time' => now()->addDays(3)->setTime(23, 59),
            'end_time' => now()->addDays(3)->setTime(23, 59),
            'is_all_day' => true,
        ]);

        Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'Sprint Planning - Proyecto Académico',
            'start_time' => now()->addDays(7)->setTime(10, 0),
            'end_time' => now()->addDays(7)->setTime(12, 0),
            'is_all_day' => false,
        ]);
    }

    private function seedTestUserData(User $user): void
    {
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Proyecto de Prueba',
            'color' => '#ef4444',
        ]);

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'title' => 'Tarea de ejemplo',
            'status' => TaskStatus::Pendiente,
            'priority' => TaskPriority::Media,
            'due_date' => now()->addDays(5),
        ]);

        Subtask::factory()->create([
            'task_id' => $task->id,
            'title' => 'Subtarea 1',
            'is_completed' => false,
        ]);

        Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'Evento de prueba',
            'start_time' => now()->addDays(2)->setTime(9, 0),
            'end_time' => now()->addDays(2)->setTime(10, 0),
            'is_all_day' => false,
        ]);
    }

    private function seedMinimalData(User $user): void
    {
        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }
}
