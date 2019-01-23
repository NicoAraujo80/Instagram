require 'watir'

followersAccepted = 0
username = ARGV[0]
password = ARGV[1]

browser = Watir::Browser.new :chrome

browser.goto "instagram.com/accounts/login"

browser.text_field(:name => 'username').set "#{username}"
browser.text_field(:name => 'password').set "#{password}"

sleep(1)
browser.button(:type => 'submit').click
sleep(2)

# closes the get notifications notification if it excists
if browser.div(:text => "Get notifications when you have new followers, likes or comments you may have missed.").exists?
	browser.button(:text => "Not Now").click
end

sleep(1)

# closes the get the app notification if it excists
if browser.div(:text => "Experience the best version of Instagram by getting the app.").exists?
	browser.span(:'aria-label' => "Close").click
end
sleep(1)

browser.a(:href => "/accounts/activity/").click

sleep(1)

# closes the 
if browser.span(:text => "Follow Requests").exists?
	browser.span(:text => "Follow Requests").click

    while browser.button(:text => 'Approve').exists?
        browser.button(:text => 'Approve').click
        followersAccepted += 1
    end
    puts(followersAccepted)
else
	puts("no follow requests right now")
end
sleep(1)
