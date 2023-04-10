import threading
import time


def run(thread_count):
    print("OK--" + str(thread_count))
    lock.acquire()
    time.sleep(1)
    thread_count -= 1
    lock.release()


def test(thread_count):
    for i in range(thread_count):
        t = threading.Thread(target=run, kwargs={"thread_count": thread_count})
        t.start()


if __name__ == '__main__':
    thread_count = 1
    lock = threading.Lock()
    test(thread_count)
