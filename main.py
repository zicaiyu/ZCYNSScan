import argparse
import os
import sys
from threading import Thread

urlList = []
pocList = []
targetList = []
targetStepList = []
num = 0

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("-f", "--file", type=str, help="url文件路径")
    parser.add_argument("-p", "--poc", type=str, help="poc名，多个以,隔开")
    parser.add_argument("-t", "--thread", type=int, help="线程数量(默认是1)", default=1)
    args = parser.parse_args()
    file = args.file
    poc = args.poc
    thread = args.thread
    if len(sys.argv) < 1:
        print("Usage python3 main.py -h 查看帮助")
    else:
        ZCYNSScan(file, poc, thread)


def ZCYNSScan(file, poc, thread):
    if file is not None:
        scanFile(file)
    if poc is not None:
        loadPoc(poc)
    else:
        ps = os.listdir("pocs")
        ps = [p for p in ps if ".py" in p]
        for p in ps:
            p = "pocs/" + p.replace(".py","")
            pocList.append(p)
    startScan(thread)

def scanFile(file):
    with open(file, "r", encoding="utf-8") as f:
        for line in f.readlines():
            domain = line.strip()
            urlList.append(domain)


def loadPoc(poc):
    pocs = poc.split(',')
    for p in pocs:
        if p + ".py" in os.listdir("pocs"):
            p = "pocs/" + p
            pocList.append(p)
        else:
            print(p + ":无效poc")
            sys.exit(0)


def startScan(thread):
    urlTempList = list(set(urlList))
    pocTempList = list(set(pocList))
    for url in urlTempList:
        for poc in pocTempList:
            targetList.append((url, poc))
    total = len(targetList)
    if total < thread:
        threadNum = total
    else:
        threadNum = thread
    multithreadTask(threadNum, total)


def multithreadTask(threadNum, total):
    threadEqualization(threadNum, total)
    for step in range(0, len(targetStepList)):
        t = Thread(target=runTask, args={targetStepList[step], total})
        t.start()
    t.join()


def threadEqualization(threadNum, total):
    for i in range(0, total, int(total / threadNum)):
        targetStepList.append(targetList[i:i + int(total / threadNum)])


def runTask(targets, total):
    global num
    sys.path.insert(0, 'pocs')
    for target in targets:
        url = target[0]
        poc = target[1]
        if "http" not in url:
            url = "http://" + url
            if url[-1] != "/":
                url = url + "/"
        pocName = poc.split("/")[1]
        try:
            module = __import__(pocName)
            flag = module.doCheck(url)
            if flag:
                print(f"[++++++]    {url}   {pocName}")
                ff = open("result.txt", 'a', encoding="utf-8")
                ff.write(f"{url}     {pocName}\n")
                ff.close()
            else:
                print(f"[------]    {url}   {pocName}")
        except Exception as e:
            print(f"[!!!!!!]    {url}   {pocName}   {e}")
        num = num + 1
        progress = "进度：" + str(num) + "/" + str(total) + "  " + "百分比：{:.3f}%".format(num / total * 100)
        print(progress, end="")
        print("\b" * (len(progress) * 2), end="", flush=True)  # 退格


def logo():
    print('''
   __________  _______ ___      ________     ___ ________ ________
  | ____    // _______||  |    |  ||     \   |  |/ ______|/ ______|
     /    / |  |        \  \  /  / |  |\  \  |  || (_____ | (_____    ___  __ _  _ __  
    /    /  |  |          \    /   |  | \  \ |  |\______ \ \_____ \  / __|/ _` || '__ \ 
  _/   /___ |  |_______    |  |    |  |  \  \|  |_______)| ______) || (__| (_| || | | |
 |_________| \_________|   |__|    |__|   \_____||_______/|_______/  \___|\__,_||_| |_|
 
 
                                                       ---by ZCY\r
    ''')


if __name__ == "__main__":
    logo()
    main()
