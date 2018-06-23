<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 *
 * @method \App\Model\Entity\Event[] paginate($object = null, array $settings = [])
 */
class EventsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $events = $this->paginate($this->Events);

        $this->set(compact('events'));
        $this->set('_serialize', ['events']);
    }

    /**
     * View method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => ['StuAttendances' => ['Students']]
        ]);

        $num_attendance = $this->Events->stuAttendances->find('all', ['contain' => ['Students', 'Events']])
        ->where([
          '_event_id' => $id
        ]);
        $num_attendance = $num_attendance->count();

        $this->set('num_attendance', $num_attendance);
        $this->set('event', $event);
        $this->set('_serialize', ['event']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $event = $this->Events->newEntity();
        if ($this->request->is('post')) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $this->set(compact('event'));
        $this->set('_serialize', ['event']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $this->set(compact('event'));
        $this->set('_serialize', ['event']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $event = $this->Events->get($id);
        if ($this->Events->delete($event)) {
            $this->Flash->success(__('The event has been deleted.'));
        } else {
            $this->Flash->error(__('The event could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * list method
     *
     * @return \Cake\Http\Response|void
     */
    public function list()
    {
      $current_date = date('Y-m-d H:i:s');
      $num_attendance = $this->Events->find();
      $num_attendance->select(['student' => $num_attendance->func()->count('__id_stu_attendance')])
      ->leftJoinWith('stuAttendances')
      ->where(['date_app_s <= ' => $current_date , 'date_app_e >= ' => $current_date , 'flg_open_web' => true])
      ->group(['_event_id'])
      ->enableAutoFields(true); // 3.4.0 より前は autoFields(true); を使用

        $events = $this->paginate($num_attendance);

        $this->set(compact('events'));
        $this->set('_serialize', ['events']);
    }

    /**
     * yayaku method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function yoyaku($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => ['StuAttendances' => ['Students']]
        ]);

        $num_attendance = $this->Events->stuAttendances->find('all', ['contain' => ['Students', 'Events']])
       ->where([
         '_event_id' => $id
       ]);
       $num_attendance = $num_attendance->count();

        $this->set('num_attendance', $num_attendance);
        $this->set('event', $event);
        $this->set('_serialize', ['event']);
    }

    /**
     * beforeFilter
     * @param  Event  $event イベントオブジェクト
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['list', 'yoyaku', 'index', 'view', 'edit', 'add']);
    }
}
