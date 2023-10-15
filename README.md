# KeyAuth User Dashboard

![Dashboard Preview](https://cdn.discordapp.com/attachments/966140909580345434/1163231503459815424/image.png)

## Necessary Modifications

1. In the path `assets/authdata/credentials.php`, add the following information:
   ```php
   $name = ""; // Application name
   $OwnerId = ""; // Application ownerID
   $SellerKey = ""; // Application's seller key. Found in seller settings (You must have a seller plan)
   $version = ""; // Panel Version
   ```

2. In the `dashboard/home.php` files, edit lines 393, 402, and 411 by replacing `https://your-url.uk` with the appropriate download URL.

3. In the `dashboard/pages/status.php` file, edit lines 395 and 404 with the download URL.

## Description

This is a web panel for the KeyAuth client interface. It is built using PHP, CSS, JS, and HTML.

## Features

This web panel offers a range of features to enhance the user experience in managing their subscription:

1. **HWID Reset**: Users can easily reset their hardware identifier (HWID) to ensure a smooth experience on different devices.

2. **Remaining Time Display**: Users can clearly and concisely view the remaining time of their subscription, keeping them informed of when they need to renew.

3. **File Downloads**: Simplifies the download of subscription-related files, providing quick access to essential resources.

4. **Enhanced Experience**: The panel has been designed with usability and user experience in mind, ensuring intuitive and efficient navigation.

These features combine to provide users with full control over their subscription and a more satisfying experience in using your service.

## Installation and Usage

To use this web panel in any hosting environment with Apache, make sure PHP 7.4 or a higher version is available on your server. Follow these steps for installation and usage:

1. **Download the Code**: Clone this repository or download the files to your hosting server.

   ```bash
   git clone https://github.com/zarfalaxd/keyauth-user-dashboard.git
   ```

2. **Customization**:

   - Edit any other necessary files or configurations to tailor the panel to your specific needs.

3. **Upload the Panel**: Upload the files to your web server or hosting.

4. **Access the Panel**: Open your web browser and navigate to the URL where you have uploaded the panel files.

5. **Login**: Log in with the appropriate credentials or authentication system you have implemented.

6. **Enjoy the Features**: Once logged in, you can use the panel's features, such as resetting HWID, checking the remaining subscription time, and downloading files.

## License

This project is under the MIT license. You can find the complete license [here](LICENSE).

---

If you find this project useful, please consider supporting it by buying me a coffee so that I can dedicate more time to open-source projects like this:

<a href="https://www.buymeacoffee.com/zarfala" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: auto !important;width: auto !important;" ></a>