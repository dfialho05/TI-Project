import cv2

# URL da webcam
webcam_url = "https://rooftop.tryfail.net/image.jpeg"

cap = cv2.VideoCapture(webcam_url)
ret, frame = cap.read()
if ret:
  # escreve na mesma diretoria do script python o ficheiro webcam.jpg
  cv2.imwrite('captura.jpg', frame)
cap.release()